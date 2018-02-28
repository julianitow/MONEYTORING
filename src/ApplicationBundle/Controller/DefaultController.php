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
use Symfony\Component\HttpFoundation\Session\Session;


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
        $manager = $this->getDoctrine()->getManager();
        $repositoryFraction = $manager->getRepository('ApplicationBundle:Fraction');
        //$partition = $repositoryFraction->findById(2);

        //var_dump($partition);

        return $this->render('@Application/Default/index.html.twig', ['id' => $id, 'prenom'=>$prenom, 'error' => $error]);
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
}