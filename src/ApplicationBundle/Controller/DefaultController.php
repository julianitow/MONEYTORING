<?php

namespace ApplicationBundle\Controller;

use ApplicationBundle\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('@Application/Default/index.html.twig');
    }
    public function connexionAction(Request $request)
    {
        $utilisateur = new Utilisateur();
        $utilisateur->setEmail('Entrer email');
        $utilisateur->setMotDePasse('mot de passe');

        $form = $this->createFormBuilder($utilisateur)
            ->add('email', TextType::class)
            ->add('MotDePasse', PasswordType::class)
            ->add('Connexion', SubmitType::class, array('label' => 'Se connecter'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $utilisateur = $form->getData();

        }

        return $this->render('@Application/Default/connexion.html.twig', [
            'form'=>$form->createView(),
            'utilisateur'=>$utilisateur,
        ]);
    }
}
