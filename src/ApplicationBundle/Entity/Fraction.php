<?php

namespace ApplicationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * fraction
 *
 * @ORM\Table(name="fraction")
 * @ORM\Entity(repositoryClass="ApplicationBundle\Repository\FractionRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"fraction" = "Fraction", "sousFraction" = "SousFraction"})
 */
class Fraction
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
     * @ORM\Column(name="nom", type="string", length=50, unique=false)
     */
    private $nom;

    /**
     * @var int
     *
     * @ORM\Column(name="montant", type="float", nullable=true)
     */
    private $montant;

    /**
     * @var string
     *
     * @ORM\Column(name="couleur", type="string", length=30)
     */
    private $couleur;

    /**
     * @var int
     *
     * @ORM\Column(name="priorite", type="integer")
     */
    private $priorite;

    /**
     * @var Utilisateur
     *
     * @ORM\ManyToOne(targetEntity="ApplicationBundle\Entity\Utilisateur")
     */
    private $utilisateur;

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
     * @return fraction
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
     * Set montant
     *
     * @param integer $montant
     *
     * @return fraction
     */
    public function setMontant($montant)
    {
        $this->montant = $montant;

        return $this;
    }

    /**
     * Get montant
     *
     * @return int
     */
    public function getMontant()
    {
        return $this->montant;
    }

    /**
     * Set couleur
     *
     * @param string $couleur
     *
     * @return fraction
     */
    public function setCouleur($couleur)
    {
        $this->couleur = $couleur;

        return $this;
    }

    /**
     * Get couleur
     *
     * @return string
     */
    public function getCouleur()
    {
        return $this->couleur;
    }

    /**
     * Set priorite
     *
     * @param integer $priorite
     *
     * @return fraction
     */
    public function setPriorite($priorite)
    {
        $this->priorite = $priorite;

        return $this;
    }

    /**
     * Get priorite
     *
     * @return int
     */
    public function getPriorite()
    {
        return $this->priorite;
    }

    /**
     * Set utilisateur
     *
     * @param \ApplicationBundle\Entity\Utilisateur $utilisateur
     *
     * @return Fraction
     */
    public function setUtilisateur(\ApplicationBundle\Entity\Utilisateur $utilisateur = null)
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    /**
     * Get utilisateur
     *
     * @return \ApplicationBundle\Entity\Utilisateur
     */
    public function getUtilisateur()
    {
        return $this->utilisateur;
    }
}
