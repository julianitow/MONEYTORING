<?php

namespace ApplicationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('@Application/Default/index.html.twig');
    }
    public function connexionAction($email, $mdp)
    {
        $utilisateur = new Utilisateur();
        $utilisateur->setUtilisateur('Caca boudin');

        $form = $this->createFormBuilder($task)
            ->add('task', TextType::class)
            ->add('dueDate', DateType::class)
            ->add('save', SubmitType::class, array('label' => 'Create Task'))
            ->getForm();

        return $this->render('@Application/Default/connexion.html.twig');
    }
}
