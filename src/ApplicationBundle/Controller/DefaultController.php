<?php

namespace ApplicationBundle\Controller;

use ApplicationBundle\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Form\Extension\Core\Type\FormType;
=======
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
>>>>>>> a61ddb97dd130aa947a2f6244cd4130cf6324a0f
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

        $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $utilisateur);

        $formBuilder
            ->add('email', TextType::class)
            ->add('motDePasse', TextType::class)
            ->add('Valider', SubmitType::class);

        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $utilisateur = $form->getData();
        }

        return $this->render('@Application/Default/connexion.html.twig', ['form'=> $form->createView(), 'utilisateur'=> $utilisateur]);
    }
}
