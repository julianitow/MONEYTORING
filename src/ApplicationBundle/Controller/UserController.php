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


class UserController extends Controller
{
    public function connexionAction(Request $request)
    {

        $user = new Utilisateur();

        $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $user);

        $formBuilder
            ->add('email', EmailType::class, ['label'=> false, 'attr' => ['placeholder' => "Adresse e-mail"]])
            ->add('motDePasse', PasswordType::class, ['label'=> false, 'attr' => ['placeholder' => "Mot de Passe"]])
            ->add('Se connecter', SubmitType::class )
            ->add('S`inscrire', ButtonType::class );

        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $user = $form->getData();   
            $manager = $this->getDoctrine()->getManager();
            $repositoryUsers = $manager->getRepository('ApplicationBundle:Utilisateur');
            $utilisateur = $repositoryUsers->findByEmail($user->getEmail(), $user->getMotDePasse());
            
        }

        return $this->render('@Application/User/connexion.html.twig', ['form'=> $form->createView(), 'utilisateur' => $utilisateur]);
    }
    public function inscriptionAction(Request $request)
    {

    }

}