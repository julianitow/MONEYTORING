<?php

namespace ApplicationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Mouvement
 *
 * @ORM\Table(name="mouvement")
 * @ORM\Entity(repositoryClass="ApplicationBundle\Repository\MouvementRepository")
 */
class Mouvement
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
     * @var int
     *
     * @ORM\Column(name="montant", type="integer")
     */
    private $montant;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=50)
     */
    private $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @var recurrence
     *
     * @ORM\ManyToOne(targetEntity="ApplicationBundle\Entity\Recurrence")
     */
    private $recurrence;

    /**
     * @var fraction
     *
     * @ORM\ManyToOne(targetEntity="ApplicationBundle\Entity\Fraction")
     */
    private $fraction;

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
     * @return Mouvement
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
     * @return Mouvement
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
     * Set type
     *
     * @param string $type
     *
     * @return Mouvement
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return Mouvement
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set fraction
     *
     * @param \ApplicationBundle\Entity\Fraction $fraction
     *
     * @return Mouvement
     */
    public function setFraction(\ApplicationBundle\Entity\Fraction $fraction = null)
    {
        $this->fraction = $fraction;

        return $this;
    }

    /**
     * Get fraction
     *
     * @return \ApplicationBundle\Entity\Fraction
     */
    public function getFraction()
    {
        return $this->fraction;
    }

    /**
     * Set recurrence
     *
     * @param \ApplicationBundle\Entity\Recurrence $recurrence
     *
     * @return Mouvement
     */
    public function setRecurrence(\ApplicationBundle\Entity\Recurrence $recurrence = null)
    {
        $this->recurrence = $recurrence;

        return $this;
    }

    /**
     * Get recurrence
     *
     * @return \ApplicationBundle\Entity\ApplicationBundle/Recurrence
     */
    public function getRecurrence()
    {
        return $this->recurrence;
    }
}
