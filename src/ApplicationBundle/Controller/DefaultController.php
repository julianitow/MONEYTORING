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
use Symfony\Component\Form\Extension\Core\Type\HiddenType;



class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        $session = $request->getSession();
        $id = $session->get('id');
        $prenom = $session->get('prenom');
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
        $partition = $repositoryFraction->findByUserID($id);

        //FORMULAIRE DE CREATION DE MOUVEMENT
        $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $mouvement);

        $formBuilder
            ->add('nom', TextType::class, ['label'=>false, 'attr' => ['placeholder' => "Nom Mouvement"]])
            ->add('montant', HiddenType::class, ['label'=>false, 'attr'=> ['placeholder' => "Montant du mouvement"]])
            ->add('type', ChoiceType::class, ['choices' => ['Sortie' => 'Sortie', 'Rentrée' => 'Rentree']])
            ->add('date', DateType::class, ['format' => 'dd-MM-yyyy', 'placeholder' => ['year' => 'Annee', 'month' => 'Mois', 'day' => 'Jour']])
            ->add('fraction', ChoiceType::class,
            ['choices' =>
            []
            ])
            ;
            $form = $formBuilder->getForm();
            $form->handleRequest($request);

        return $this->render('@Application/Default/index.html.twig', ['form' => $form->createView(), 'id' => $id, 'prenom'=>$prenom, 'fraction'=>$partition, 'error' => $error]);
    }

    public function partitionAction(Request $request)
    {
        //VERIFICATION DE CONNEXION
        $session = $request->getSession();
        $id = $session->get('id');
        $prenom = $session->get('prenom');
        $email = $session->get('email');
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


        return $this->render('@Application/Default/partition.html.twig', ['form' => $form->createView(), 'prenom' => $prenom, 'utilisateur' => $fraction, 'error'=>$error]);
    }

    public function simulationAction()
    {
        return $this->render('@Application/Default/simulation.html.twig');
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
