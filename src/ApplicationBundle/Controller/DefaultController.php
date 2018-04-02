<?php

namespace ApplicationBundle\Controller;
//ENTITES
use ApplicationBundle\Entity\Utilisateur;
use ApplicationBundle\Entity\Fraction;
use ApplicationBundle\Entity\Mouvement;
use ApplicationBundle\Entity\Projet;

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
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

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
        $budgetRestant = $budgetGlobal;

        foreach ($partitions as $partition)
        {
            $mouvements[$partition->getId()] = $repositoryMouvement->findByFraction($partition->getId());
            $montant[$partition->getId()] = null;

                foreach($mouvements[$partition->getId()] as $mouvementCalc)
                {
                  if ($mouvementCalc->getType() == "Sortie")
                  {
                    $budgetRestant = $budgetRestant - $mouvementCalc->getMontant();
                  }
                  elseif ($mouvementCalc->getType() == "Rentree")
                  {
                    $budgetRestant = $budgetRestant + $mouvementCalc->getMontant();
                  }

                  if ($mouvementCalc->getFraction()->getId() == $partition->getId())
                  {
                      $montant[$partition->getId()] += $mouvementCalc->getMontant();
                  }
                }
        }
        //en cas de budget restant négatif
        if ($budgetRestant <= 0)
        {
          $error = "nullBudget";
        }
        //Après l'inscription, comme aucun mouvement dans la base, pour éviter l'erreur
        if (!(isset($mouvements)))
        {
          $mouvements = null;
        }

        $session->set('mouvements', $mouvements);
        $session->set('montant', $montant);
        $session->set('budgetGlobal', $budgetGlobal);


        //FORMULAIRE DE CREATION DE MOUVEMENT
        $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $mouvement);

        $formBuilder
            ->add('nom', TextType::class, ['label'=>'Nom', 'attr' => ['placeholder' => '"Changement pneus"']])
            ->add('montant', MoneyType::class, ['label' => 'Montant', 'currency' => null, 'scale' => 4, 'attr'=>['placeholder' => '"400"']])
            ->add('date', DateType::class, ['label' => 'Date' , 'format' => 'dd-MM-yyyy', 'placeholder' => ['year' => 'Annee', 'month' => 'Mois', 'day' => 'Jour']])
            ->add('type', ChoiceType::class, ['label' => 'Type', 'choices' => ['Sortie' => 'Sortie', 'Rentrée' => 'Rentree'], 'attr' =>["onchange"=>"itemChange()"]])
            ->add('fraction', EntityType::class,['label' => 'Partition associée','class' => 'ApplicationBundle:Fraction','query_builder' => function(\ApplicationBundle\Repository\FractionRepository $repo) use ($id)
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
                //application des changements en base de donnés
                $manager->persist($mouvement);
                $manager->flush();
                return $this->redirectToRoute('application_homepage');
              }
              catch(DBALException $e)
              {
                $error = "DBALException";
              }
            }

        return $this->render('@Application/Default/index.html.twig', ['form' => $form->createView(), 'montant' => $montant, 'id' => $id, 'prenom'=>$prenom, 'fractions'=>$partitions, 'budgetRestant' => $budgetRestant, 'mouvements' => $mouvements, 'error' => $error]);
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
            //->add('montant', TextType::class, ['label'=>false, 'attr' => ['placeholder' => "Montant de la partition"]])
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

          //CONDITION SI MOUVEMENT DE RENTREE
          /*if ($fraction->getType() == "Rentree")
          {
            $fraction->setFraction(0);
          }*/

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
        $id = $session->get('id');
        $prenom = $session->get('prenom');
        $montant = $session->get('montant');
        $budgetGlobal = $session->get('budgetGlobal');

        //RECUPERATION DES FRACTIONS:
        $manager = $this->getDoctrine()->getManager();
        $repositoryFraction = $manager->getRepository('ApplicationBundle:Fraction');
        $repositoryProjet = $manager->getRepository('ApplicationBundle:Projet');

        $projet = new Projet();

        $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $projet);

        //BOUTON DE PROGRAMMATION D'UN PROJET
        $formBuilder
            ->add('nom', EntityType::class,['label' => 'Projet à afficher : ','class' => 'ApplicationBundle:Projet','query_builder' => function(\ApplicationBundle\Repository\ProjetRepository $repo) use ($id)
            {
              return $repo->findAllByUserID($id);
            }
            , 'choice_label' => 'nom'])
            ->add('Programmer', SubmitType::class, ['attr' => ['class'=> 'btn btn-primary']]);

            ;

        $formProgrammer = $formBuilder->getForm();
        $formProgrammer->handleRequest($request);

        if ($formProgrammer->isSubmitted() && $formProgrammer->isValid())
        {
          $projetSelectionne = $formProgrammer->getData();
        }

        $projets = $repositoryProjet->findByUserID($id);

        $fractions = $repositoryFraction->findByUserID($id);

        //APRES ISNCRIPTION VERIFICATION DE L'EXISTANCE DE PROJET(S)
        if (!(isset($projetSelectionne)))
        {
          $projetSelectionne = null;
        }

        return $this->render('@Application/Default/simulation.html.twig', ['formProgrammer' => $formProgrammer->createView(), 'prenom' => $prenom, 'montant'=>$montant, 'budgetGlobal'=>$budgetGlobal, 'fractions' => $fractions, 'projetSelectionne' => $projetSelectionne, 'projets' => $projets, 'error' => $error]);
    }

    public function entrerMouvementAction()
    {
        return $this->render('@Application/Default/entrerMouvement.html.twig');
    }

    public function modifierMouvementAction()
    {
        return $this->render('@Application/Default/modifierMouvement.html.twig');
    }

    public function aideAction(Request $request)
    {
        $session = $request->getSession();
        $prenom = $session->get('prenom');
        $error = null;
        return $this->render('@Application/Default/aide.html.twig', ['prenom' => $prenom, 'error' => $error]);
    }
}
