<?php

namespace ApplicationBundle\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

use Doctrine\ORM\Mapping as ORM;

/**
 * utilisateur
 *
 * @ORM\Table(name="utilisateur")
 * @ORM\Entity(repositoryClass="ApplicationBundle\Repository\UtilisateurRepository")
 */
class Utilisateur implements UserInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=50)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=50)
     */
    private $prenom;

    /**
     * @Assert\Length(max=4096)
     */
    private $motDePasseClair;

    /**
     * @var string
     *
     * @ORM\Column(name="motDePasse", type="string", length=200)
     */
    private $motDePasse;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     */
    private $email;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateNaiss", type="date")
     */
    private $dateNaiss;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=20, nullable = true, options={"default" : null})
     */
    private $token;

    /**
     * @var int
     *
     * @ORM\Column(name="budgetGlobal", type="integer", nullable = true, options={"default" : null})
     */
    private $budgetGlobal;

    /**
     * @var bool
     *
     * @ORM\Column(name="daltonisme", type="boolean", nullable = true, options={"default" : false})
     */
    private $daltonisme;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return utilisateur
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     *
     * @return utilisateur
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set motDePasse
     *
     * @param string $motDePasseClair
     *
     * @return utilisateur
     */
    public function setMotDePasseClair($motDePasseClair)
    {
        $this->motDePasseClair = $motDePasseClair;

        return $this;
    }

    /**
     * Get motDePasseClair
     *
     * @return string
     */
    public function getMotDePasseClair()
    {
        return $this->motDePasseClair;
    }

    /**
     * Set motDePasse
     *
     * @param string $motDePasse
     *
     * @return utilisateur
     */
    public function setMotDePasse($motDePasse)
    {
        $this->motDePasse = $motDePasse;

        return $this;
    }

    /**
     * Get motDePasse
     *
     * @return string
     */
    public function getMotDePasse()
    {
        return $this->motDePasse;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return utilisateur
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set dateNaiss
     *
     * @param \DateTime $dateNaiss
     *
     * @return utilisateur
     */
    public function setDateNaiss($dateNaiss)
    {
        $this->dateNaiss = $dateNaiss;

        return $this;
    }

    /**
     * Get dateNaiss
     *
     * @return \DateTime
     */
    public function getDateNaiss()
    {
        return $this->dateNaiss;
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return utilisateur
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set budgetGlobal
     *
     * @param integer $budgetGlobal
     *
     * @return utilisateur
     */
    public function setBudgetGlobal($budgetGlobal)
    {
        $this->budgetGlobal = $budgetGlobal;

        return $this;
    }

    /**
     * Get budgetGlobal
     *
     * @return int
     */
    public function getBudgetGlobal()
    {
        return $this->budgetGlobal;
    }

    /**
     * Set daltonisme
     *
     * @param boolean $daltonisme
     *
     * @return utilisateur
     */
    public function setDaltonisme($daltonisme)
    {
        $this->daltonisme = $daltonisme;

        return $this;
    }

    /**
     * Get daltonisme
     *
     * @return bool
     */
    public function getDaltonisme()
    {
        return $this->daltonisme;
    }

    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    public function getRoles()
    {
        return array('ROLE_USER');
    }

    public function eraseCredentials()
    {
    }

    public function getPassword()
    {
        return $this->getMotDePasse();
    }

    public function getUsername()
    {
        return $this->getNom();
    }

}
