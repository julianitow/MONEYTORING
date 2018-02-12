<?php

namespace ApplicationBundle\Controller;

use ApplicationBundle\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Form\Button;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('@Application/Default/index.html.twig');
    }

    public function partitionAction()
    {
        return $this->render('@Application/Default/partition.html.twig');
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

    public function parametresUtilisateurAction()
    {
        return $this->render('@Application/Default/parametresUtilisateur.html.twig');
    }

    public function deconnexionAction()
    {
        return $this->render('@Application/Default/deconnexion.html.twig');
    }
}
