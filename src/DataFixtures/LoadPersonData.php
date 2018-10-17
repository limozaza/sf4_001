<?php
/**
 * Created by PhpStorm.
 * User: zak
 * Date: 14/10/18
 * Time: 22:43
 */

namespace App\DataFixtures;


use App\Entity\Person;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadPersonData extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $person1 = new Person();
        $person1->setFirstName("Boufares");
        $person1->setLastName("Zakaria");
        $person1->setDateOfBirth(new \DateTime('1986-03-29'));

        $manager->persist($person1);


        $manager->flush();
    }
}