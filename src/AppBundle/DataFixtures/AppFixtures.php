<?php
/**
 * Created by PhpStorm.
 * User: vaz
 * Date: 1/26/18
 * Time: 3:46 PM
 */

namespace AppBundle\DataFixtures;

use AppBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // user
        $user = new User();
        $user->setEmail('alice@gmail.com')
            ->setEnabled(true)
            ->setPlainPassword('password');

        $manager->persist($user);
        $manager->flush();
    }
}