<?php

namespace ApplicationBundle\Controller;

//ENTITE / CONTROLLER ET ERREUR

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use ApplicationBundle\Entity\Utilisateur;
use Doctrine\ORM\NoResultException;

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
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;


//POUR ENCODAGE PASSWORD
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

//POUR LES SESSIONS

use Symfony\Component\HttpFoundation\Session\Session;

class UserController extends Controller
{
    public function connexionAction(Request $request)
    {
        $error = null; // pour éviter le "undefined variable error"

        $user = new Utilisateur(); //création d' un objet utilisater vide 

        $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $user); // Initisalisation du form builder 

        //CREATION DU FORMULAIRE
        $formBuilder
            ->add('email', EmailType::class, ['label'=> false, 'attr' => ['placeholder' => "Adresse e-mail"]])
            ->add('motDePasseClair', PasswordType::class, ['label'=> false, 'attr' => ['placeholder' => "Mot de Passe"]])
            ->add('Se connecter', SubmitType::class, ['attr' => ['class'=> 'btn btn-primary']]);

        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $user = $form->getData();
            $motDePasseSaisie = $user->getMotDePasseClair();
            $manager = $this->getDoctrine()->getManager();
            $repositoryUsers = $manager->getRepository('ApplicationBundle:Utilisateur');
            //VERIFICATION HASH PASSWORD
            $passwordEncoder = $this->container->get('security.password_encoder');
            //Récupération du mit de passe crypté
            $hashedPassword = $repositoryUsers->findByEmail($user->getEmail()/*, $user->getMotDePasseClair()*/);

            //verification du resultat de la requete
            if ($hashedPassword != "NoResultException")
            {
                $user->setMotDePasse($hashedPassword["motDePasse"]);
            }
            else
            {
                $error = "NoResultException";

            }
            //Verification du mot de passe récupéré avec celui saisie
            if ($passwordEncoder->isPasswordValid($user, $motDePasseSaisie))
            {
                 $user = $repositoryUsers->findOneByEmail($user->getEmail());
                 $error = "NoError";
                 return $this->redirectToRoute('application_homepage');
            }
            else
            {
                $error = "NoResultException";
            }           
        }

        return $this->render('@Application/User/connexion.html.twig', ['form'=> $form->createView(), 'utilisateur' => $user, 'error' => $error]);
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
            ->add('MotDePasseClair', RepeatedType::class, ['type' => PasswordType::class, 'first_options' => ['label'=> "Mot de passe", 'attr' => ['placeholder' => "Mot de Passe"]], 'second_options' => ['label'=> "Répetez mot de passe", 'attr' => ['placeholder' => "Vérification"]]])
            ->add('Inscription', SubmitType::class, ['attr' => ['class'=> 'btn btn-primary']] );



        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {   
            $user = $form->getData();
            //HASHAGE
            $passwordEncoder = $this->get('security.password_encoder');
            $motDePasse = $passwordEncoder->encodePassword($user, $user->getMotDePasseClair());
            $user->setMotDePasse($motDePasse);

            $manager = $this->getDoctrine()->getManager();
            $repositoryUsers = $manager->getRepository('ApplicationBundle:Utilisateur');
            $manager->persist($user);

            try 
            {
                $manager->flush();
                return $this->redirectToRoute('connexion');
            }
            catch (PDOException $e)
            {
                $error = "UniqueConstraintViolationException";
            }
            catch (UniqueConstraintViolationException $e)
            {
                $error = "UniqueConstraintViolationException";

            }
        }

        return $this->render('@Application/User/inscription.html.twig', ['form'=> $form->createView(), 'error'=> $error]);
    }

}