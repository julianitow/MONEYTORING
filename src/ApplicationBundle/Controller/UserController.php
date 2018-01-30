<?php

namespace ApplicationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ApplicationBundle\Entity\Utilisateur;
use Doctrine\ORM\NoResultException;
//use Doctrine\ORM\Query\ResultSetMappingBuilder;


//CONTENU FORMULAIRE
use Symfony\Component\Form\Button;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;

class UserController extends Controller
{
    public function connexionAction(Request $request)
    {

        $user = new Utilisateur();

        $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $user);

        $formBuilder
            ->add('email', EmailType::class, ['label'=> false, 'attr' => ['placeholder' => "Adresse e-mail"]])
            ->add('motDePasse', PasswordType::class, ['label'=> false, 'attr' => ['placeholder' => "Mot de Passe"]])
            ->add('Se connecter', SubmitType::class, ['attr' => ['class'=> 'btn btn-primary']]);

        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $user = $form->getData();
            //HASH PASSWORD:
            $clearPassword = $user->getMotDePasse();
            $encodedPassword = $encoder->encodePassword($user, $clearPassword);
            $user->setMotDePasse($encodePassword);

            $manager = $this->getDoctrine()->getManager();
            $repositoryUsers = $manager->getRepository('ApplicationBundle:Utilisateur');
            $user = $repositoryUsers->findByEmail($user->getEmail(), $user->getMotDePasse());
            
        }

        return $this->render('@Application/User/connexion.html.twig', ['form'=> $form->createView(), 'utilisateur' => $user]);
    }
    public function inscriptionAction(Request $request)
    {
        $error = null;

        $user = new Utilisateur();

        $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $user);

        $formBuilder
            ->add('nom', TextType::class, ['label'=> false, 'attr' => ['placeholder'=> "Nom"]])
            ->add('prenom', TextType::class, ['label'=> false, 'attr' => ['placeholder'=> "Prenom"]])
            ->add('email', EmailType::class, ['label'=> false, 'attr' => ['placeholder'=> "Adresse e-mail"]])
            ->add('dateNaiss', BirthdayType::class, ['label'=> "Date de naissance : ", 'format' => 'dd-MM-yyyy'])
            ->add('motDePasse', PasswordType::class, ['label'=> "Mot de passe", 'attr' => ['placeholder' => "Mot de Passe"]])
            ->add('Inscription', SubmitType::class, ['attr' => ['class'=> 'btn btn-primary']] );



        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $user = $form->getData();   
            $manager = $this->getDoctrine()->getManager();
            $repositoryUsers = $manager->getRepository('ApplicationBundle:Utilisateur');
            $manager->persist($user);
            try 
            {
                $manager->flush();
            }
            catch (UniqueConstraintViolationException $e)
            {
                $error = "UniqueConstraintViolationException";
            }
        }

        return $this->render('@Application/User/inscription.html.twig', ['form'=> $form->createView(), 'error'=> $error]);
    }

}