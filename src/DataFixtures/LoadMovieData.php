<?php
/**
 * Created by PhpStorm.
 * User: zak
 * Date: 14/10/18
 * Time: 22:43
 */

namespace App\DataFixtures;


use App\Entity\Movie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadMovieData extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $movie1 = new Movie();
        $movie1->setTitle('Hesi Mesi');
        $movie1->setTime(189);
        $movie1->setYear(1995);
        $movie1->setDescription('Mesreh lhayy lehriig');

        $manager->persist($movie1);


        $movie1 = new Movie();
        $movie1->setTitle('Chereh Meleh');
        $movie1->setTime(201);
        $movie1->setYear(1998);
        $movie1->setDescription('Mesreh lhayy Sbittaar');

        $manager->persist($movie1);


        $manager->flush();
    }
}