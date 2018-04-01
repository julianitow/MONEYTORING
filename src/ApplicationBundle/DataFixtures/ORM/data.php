<?php
namespace Application\DataFixtures\ORM;

use ApplicationBundle\Entity\Utilisateur;
use ApplicationBundle\Entity\Fraction;
use ApplicationBundle\Entity\SousFraction;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

    class ApplicationFixtures extends Fixture
    {
        public function load(ObjectManager $manager)
        {
            $utilisateur = new Utilisateur();
            $utilisateur->setNom('Chinnapong');
            $utilisateur->setPrenom('Didier');
            $utilisateur->setMotDePasse('couille');
            $utilisateur->setEmail('didier.chinnapong@gmail.com');
            $utilisateur->setDateNaiss(new \DateTime('03/10/1997'));
            $utilisateur->setToken(0);
            $utilisateur->setBudgetGlobal(0);
            $utilisateur->setDaltonisme(false);
            
            $manager->persist($utilisateur);

            $partition = new Fraction();
            $partition->setNom('Course');
            $partition->setMontant(100);
            $partition->setCouleur('#097867');
            $partition->setPriorite(1);
            $partition->setCategorie("fraction");

            $manager->persist($partition);

            $sousPartition = new SousFraction();
            $sousPartition->setNom('Alimentaire');
            $sousPartition->setMontant(50);
            $sousPartition->setCouleur('#098767');
            $sousPartition->setPriorite(1);
            $partition->setCategorie("sousFraction");

            $manager->persist($sousPartition);

            $manager->flush();
        }
    }