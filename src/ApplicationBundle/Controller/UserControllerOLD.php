<?php

//namespace ApplicationBundle\Controller;
namespace ApplicationBundle\UserController;

use ApplicationBundle\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;


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
        $error = null;

        $user = new Utilisateur();

        $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $user);

        $formBuilder
            ->add('email', EmailType::class, ['label'=> false, 'attr' => ['placeholder' => "Adresse e-mail"]])
            ->add('motDePasse', PasswordType::class, ['label'=> false, 'attr' => ['placeholder' => "Mot de Passe"]])
            ->add('Se connecter', SubmitType::class);

        $form = $formBuilder->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $user = $form->getData();
            $manager = $this->getDoctrine()->getManager();
            $repositoryUsers = $manager->getRepository('ApplicationBundle:Utilisateur');
            
                try
                {
                    $requete =  new ResultSetMapping();
                    $requete = addEntityResult('ApplicationBundle\Entity\Utilisateur', 'u');
                    $requete = addFieldResult('u', 'id', 'id');
                    $requete = addFieldResult('u', 'nom', 'nom');
                    $requete = addFieldResult('u', 'prenom', 'prenom');
                    $requete = addFieldResult('u', 'email', 'email');
                    $requete = addFieldResult('u', 'motDePasse', 'motDePasse');
                    $requete = addFieldResult('u', 'dateNaiss', 'dateNaiss');
                    $requete = addFieldResult('u', 'token', 'token');
                    $requete = addFieldResult('u', 'budgetGlobal', 'budgetGlobal');
                    $requete = addFieldResult('u', 'daltonisme', 'daltonisme');

                    $query = $this->$manager->createNativeQuery('SELECT id, nom, prenom, email, motDePasse, dateNaiss, token, budgetGlobal, daltonisme FROM Utilisateur WHERE email = ? && motDePasse = ?', $requete);
                    $query->setParameter(1, $user->getEmail());

                    $user = $query->getResult();


                    /* remplacement DQL
                    $user = $repositoryUsers->findOneByEmail($utilisateur->getEmail());
                    if ($utilisateur->getMotDePasse() == $user->getMotDePasse())
                    {
                        return $this->render('@Application/Default/connexion.html.twig', ['form'=> $form->createView(), 'utilisateur'=> $user]);
                    } */               
                }
                catch (\Doctrine\ORM\NoResultException $e)
                {
                    $error  = "NoResultException";
                }
            
        }

        return $this->render('@Application/Default/connexion.html.twig', ['form'=> $form->createView(), 'utilisateur' => $user, 'error' => $error]);
    }
}