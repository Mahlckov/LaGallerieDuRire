<?php

namespace App\DataFixtures;

use App\Entity\Humouriste;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class HumouristeFixtures extends Fixture
{
    private UserPasswordHasherInterface $userPasswordHasher;
    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher= $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker= \Faker\Factory::create("fr_FR");
        $admin = new Humouriste();
        $admin->setNom($faker->lastName());
        $admin->setPrenom($faker->firstName());
        $admin->setPseudo("Admin");
        $admin->setEmail("admin@mail.com");
        $admin->setPassword($this->userPasswordHasher->hashPassword($admin, 'admin'));
        $admin->setAdmin(true);
        $admin->setActif(true);

        $manager->persist($admin);
        $manager->flush();

        for ($i=1;$i<=10;$i++) {
            $humouriste = new Humouriste();
            $humouriste->setNom($faker->lastName());
            $humouriste->setPrenom($faker->firstName());
            $humouriste->setPseudo($faker->userName());

            $email_prefix = strtolower($humouriste->getPrenom() . '.' . $humouriste->getNom());
            $email_suffix = $faker->randomNumber(2) . '@' . $faker->freeEmailDomain();
            $email = $email_prefix . $email_suffix;

            $humouriste->setEmail($email);
            $humouriste->setPassword($this->userPasswordHasher->hashPassword($humouriste, 'mdp'));
            $humouriste->setAdmin(false);
            $humouriste->setActif(true);

            $manager->persist($humouriste);
        }
        $manager->flush();
    }

}
