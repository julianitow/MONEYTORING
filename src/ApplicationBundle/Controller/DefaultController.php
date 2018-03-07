<?php

namespace ApplicationBundle\Controller;
//ENTITES
use ApplicationBundle\Entity\Utilisateur;
use ApplicationBundle\Entity\Fraction;
use ApplicationBundle\Entity\Mouvement;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Form\Button;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

//EXCEPTION
use Doctrine\DBAL\DBALException;


class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $session = $request->getSession();
        $id = $session->get('id');
        $prenom = $session->get('prenom');
        $budgetGlobal = $session->get('budgetGlobal');

         if ($id == null)
        {
            $error = "ConnexionNeeded";
        }
        else
        {
            $error = null;
        }

        //RECUPERATION FRACTION
        $mouvement = new Mouvement();
        $manager = $this->getDoctrine()->getManager();
        $repositoryFraction = $manager->getRepository('ApplicationBundle:Fraction');
        $partitions = $repositoryFraction->findByUserID($id);

        //RECUPERATION DES MOUVEMENTS DES PARTITIONS LIEES
        $repositoryMouvement = $manager->getRepository('ApplicationBundle:Mouvement');
        $montant = null;
        foreach ($partitions as $partition)
        {
            $mouvements[$partition->getId()] = $repositoryMouvement->findByFraction($partition->getId());
            $montant[$partition->getId()] = null;

                foreach($mouvements[$partition->getId()] as $mouvementCalc)
                {
                  if ($mouvementCalc->getFraction()->getId() == $partition->getId())
                  {
                      $montant[$partition->getId()] += $mouvementCalc->getMontant();
                      $budgetGlobal = $budgetGlobal-$mouvementCalc->getMontant();
                  }
                }
        }
        //Après l'inscription, comme aucun mouvement dans la base, pour éviter l'erreur
        if (!(isset($mouvements)))
        {
          $mouvements = null;
        }

        $session->set('mouvements', $mouvements);


        //FORMULAIRE DE CREATION DE MOUVEMENT
        $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $mouvement);

        $formBuilder
            ->add('nom', TextType::class, ['label'=>false, 'attr' => ['placeholder' => "Nom Mouvement"]])
            ->add('montant', MoneyType::class, ['label' => false, 'scale' => 4, 'attr'=>['placeholder' => "Montant"]])
            ->add('type', ChoiceType::class, ['choices' => ['Sortie' => 'Sortie', 'Rentrée' => 'Rentree']])
            ->add('date', DateType::class, ['format' => 'dd-MM-yyyy', 'placeholder' => ['year' => 'Annee', 'month' => 'Mois', 'day' => 'Jour']])
            ->add('fraction', EntityType::class,['class' => 'ApplicationBundle:Fraction','query_builder' => function(\ApplicationBundle\Repository\FractionRepository $repo) use ($id)
            {
              return $repo->findAllByUserID($id);
            }
              , 'choice_label' => 'nom'])
            ->add('Créer', SubmitType::class, ['attr' => ['class'=> 'btn btn-primary']])
            ;
            $form = $formBuilder->getForm();
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid())
            {
              $repositoryMouvement = $manager->getRepository('ApplicationBundle:Mouvement');
              $mouvement = $form->getData();
              try{
                $manager->persist($mouvement);
                $manager->flush();
              }
              catch(DBALException $e)
              {
                $error = "DBALException";
              }
            }

        return $this->render('@Application/Default/index.html.twig', ['form' => $form->createView(), 'montant' => $montant, 'id' => $id, 'prenom'=>$prenom, 'fractions'=>$partitions, 'budgetGlobal' => $budgetGlobal, 'mouvements' => $mouvements, 'error' => $error]);
    }

    public function partitionAction(Request $request)
    {
        //VERIFICATION DE CONNEXION
        $session = $request->getSession();
        $id = $session->get('id');
        $prenom = $session->get('prenom');
        $email = $session->get('email');
        $mouvements = $session->get('mouvements');
         if ($id == null)
        {
            $error = "ConnexionNeeded";
        }
        else
        {
            $error = null;
        }
        //FORMULAIRE AJOUT PARTITION

        $fraction = new Fraction();
        $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $fraction);

        //création du formulaire d'ajout
        $formBuilder
            ->add('nom', TextType::class, ['label'=>false, 'attr' => ['placeholder' => "Nom de la partition"]])
            ->add('montant', TextType::class, ['label'=>false, 'attr' => ['placeholder' => "Montant de la partition"]])
            ->add('couleur', ChoiceType::class, ['choices' => ['rouge' => 'red', 'bleu' => 'blue', 'jaune' => 'yellow', 'orange' => 'orange']])
            ->add('priorite', ChoiceType::class, ['choices' => ['1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5]])
            ->add('Créer', SubmitType::class, ['attr' => ['class'=> 'btn btn-primary']]);
            ;

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
          $fraction = $form->getData();

          $manager = $this->getDoctrine()->getManager();

          //RECHERCHE DE L'UTILISATEUR LIE
          $utilisateurLie = new Utilisateur();
          $repositoryUsers = $manager->getRepository('ApplicationBundle:Utilisateur');
          $utilisateurLie = $repositoryUsers->findOneByEmail($email);

          //LIAISON DE L'UTILISATEUR
          $fraction->setUtilisateur($utilisateurLie);

          //APPLICATION DANS LA BASE DE DONNEES
          $repositoryFraction = $manager->getRepository('ApplicationBundle:Fraction');
          $manager->persist($fraction);
          try
          {
            $manager->flush();
            $error = "succeed";
          }
          catch (ConstraintViolationException $e)
          {
            $error = "ConstraintViolationException";
          }

        }


        return $this->render('@Application/Default/partition.html.twig', ['form' => $form->createView(), 'prenom' => $prenom, 'mouvements' => $mouvements, 'utilisateur' => $fraction, 'error'=>$error]);
    }

    public function simulationAction(Request $request)
    {
        $error = null;
        $session = $request->getSession();
        $prenom = $session->get('prenom');
        return $this->render('@Application/Default/simulation.html.twig', ['prenom' => $prenom, 'error' => $error]);
    }

    public function entrerMouvementAction()
    {
        return $this->render('@Application/Default/entrerMouvement.html.twig');
    }

    public function modifierMouvementAction()
    {
        return $this->render('@Application/Default/modifierMouvement.html.twig');
    }

    public function aideAction()
    {
        return $this->render('@Application/Default/aide.html.twig');
    }
}

//ludo : 6 gorgées sur pik
//jojo : 3 grgées sur coeur
//ju : 8 sur trefle
//pduf 5 sur pik
