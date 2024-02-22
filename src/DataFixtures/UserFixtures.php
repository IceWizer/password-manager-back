<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Create 3 users
        // Super admin user
        // Admin user
        // User user

        $sup = new User();
        $password = $this->hasher->hashPassword($sup, 'Not24get');
        $sup->setEmail('superadmin@icewize.fr');
        $sup->setRoles(['ROLE_SUPER_ADMIN']);
        $sup->setPassword($password);
        $sup->setUsername('superadmin');
        $manager->persist($sup);

        $admin = new User();
        $password = $this->hasher->hashPassword($admin, 'Not24get');
        $admin->setEmail('admin@icewize.fr');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($password);
        $admin->setUsername('admin');
        $manager->persist($admin);

        $user = new User();
        $password = $this->hasher->hashPassword($user, 'Not24get');
        $user->setEmail('jury@icewize.fr');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($password);
        $user->setUsername('jury');
        $manager->persist($user);

        $manager->flush();
    }
}
