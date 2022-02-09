<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Livreur;
use App\Entity\Restaurant;
use App\Entity\StatutCommande;
use App\Entity\Type;
use App\Entity\User;
use App\Entity\Vehicule;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Remplissage des tables "statiques"
        $statutscommandes = array("an attente", "acceptée", "prête", "prise en charge par le livreur", "livrée");
        foreach ($statutscommandes as $s) {
            $statutscommande = new StatutCommande();
            $statutscommande->setNom($s);
            $manager->persist($statutscommande);
        };

        $vehicules = array("Vélo", "Voiture", "Scooter");
        foreach ($vehicules as $v) {
            $vehicule = new Vehicule();
            $vehicule->setType($v);
            $manager->persist($vehicule);
        };

        $types = array("kebab", "chinois", "pizza", "mexicain", "hamburger", "tacos", "italien", "fast-food", "halal", "français");
        foreach ($types as $t) {
            $type = new Type();
            $type->setNom($t);
            $manager->persist($type);
        };


        // Remplissage des tables "dinamiques"
        //// Livreur 1
        $user = new User();
        $hash = $this->hasher->hashPassword($user, "password");
        $user
            ->setEmail("livreur1@fake.xx")
            ->setPassword($hash)
            ->setProfessionnel(true)
            ->setRoles(['ROLE_LIVREUR']);
        $livreur = new Livreur();
        $livreur
            ->setNom("legrand")
            ->setPrenom('jean')
            ->setTelephone("0612345789")
            ->setSecteur("Valenciennes");
        $user->setLivreur($livreur);
        $manager->persist($user);

        //// Livreur 2
        $user = new User();
        $hash = $this->hasher->hashPassword($user, "password");
        $user
            ->setEmail("livreur2@fake.xx")
            ->setPassword($hash)
            ->setProfessionnel(true)
            ->setRoles(['ROLE_LIVREUR']);
        $livreur = new Livreur();
        $livreur
            ->setNom("mira")
            ->setPrenom('leo')
            ->setTelephone("0623457891")
            ->setSecteur("Valenciennes");
        $user->setLivreur($livreur);
        $manager->persist($user);

        //// Livreur 3
        $user = new User();
        $hash = $this->hasher->hashPassword($user, "password");
        $user
            ->setEmail("livreur3@fake.xx")
            ->setPassword($hash)
            ->setProfessionnel(true)
            ->setRoles(['ROLE_LIVREUR']);
        $livreur = new Livreur();
        $livreur
            ->setNom("leblanc")
            ->setPrenom('lucie')
            ->setTelephone("0634578912")
            ->setSecteur("Petite-Forêt");
        $user->setLivreur($livreur);
        $manager->persist($user);

        //// Client 1
        $user = new User();
        $hash = $this->hasher->hashPassword($user, "password");
        $user
            ->setEmail("client1@fake.xx")
            ->setPassword($hash)
            ->setProfessionnel(false)
            ->setRoles([]);
        $client = new Client();
        $client
            ->setNom("rose")
            ->setPrenom('dany')
            ->setTelephone("0645789123")
            ->setRue("6 rue des prés")
            ->setCodePostal('59300')
            ->setVille("Valenciennes");
        $user->setClient($client);
        $manager->persist($user);

        //// Client 2
        $user = new User();
        $hash = $this->hasher->hashPassword($user, "password");
        $user
            ->setEmail("client2@fake.xx")
            ->setPassword($hash)
            ->setProfessionnel(false)
            ->setRoles([]);
        $client = new Client();
        $client
            ->setNom("mode")
            ->setPrenom('luc')
            ->setTelephone("0657891234")
            ->setRue("57 boulevard des verres")
            ->setCodePostal('59494')
            ->setVille("Petite-Forêt");
        $user->setClient($client);
        $manager->persist($user);

        //// Client 3
        $user = new User();
        $hash = $this->hasher->hashPassword($user, "password");
        $user
            ->setEmail("client3@fake.xx")
            ->setPassword($hash)
            ->setProfessionnel(false)
            ->setRoles([]);
        $client = new Client();
        $client
            ->setNom("leasec")
            ->setPrenom('anna')
            ->setTelephone("0678912345")
            ->setRue("12 impasse de meuraz")
            ->setCodePostal('59300')
            ->setVille("Valenciennes");
        $user->setClient($client);
        $manager->persist($user);

        //// Restaurant 1
        $user = new User();
        $hash = $this->hasher->hashPassword($user, "password");
        $user
            ->setEmail("resto1@fake.xx")
            ->setPassword($hash)
            ->setProfessionnel(true)
            ->setRoles(["ROLE_RESTAURATEUR"]);
        $restaurant = new Restaurant();
        $restaurant
            ->setRaisonSociale('Chaudron baveur')
            ->setTelephone("0689123457")
            ->setRue("16 place de grands hommes")
            ->setComplement('')
            ->setCodePostal('59300')
            ->setVille("Valenciennes");
        $user->setRestaurant($restaurant);
        $manager->persist($user);

        //// Restaurant 2
        $user = new User();
        $hash = $this->hasher->hashPassword($user, "password");
        $user
            ->setEmail("resto2@fake.xx")
            ->setPassword($hash)
            ->setProfessionnel(true)
            ->setRoles(["ROLE_RESTAURATEUR"]);
        $restaurant = new Restaurant();
        $restaurant
            ->setRaisonSociale('Horny Torinque')
            ->setTelephone("0691234578")
            ->setRue("6 boulevard de la roue")
            ->setComplement('square des fleurs')
            ->setCodePostal('59300')
            ->setVille("Valenciennes");
        $user->setRestaurant($restaurant);
        $manager->persist($user);

        //// Restaurant 3
        $user = new User();
        $hash = $this->hasher->hashPassword($user, "password");
        $user
            ->setEmail("resto3@fake.xx")
            ->setPassword($hash)
            ->setProfessionnel(true)
            ->setRoles(["ROLE_RESTAURATEUR"]);
        $restaurant = new Restaurant();
        $restaurant
            ->setRaisonSociale('Chez momo')
            ->setTelephone("0698754321")
            ->setRue("47 avenue verte")
            ->setComplement('')
            ->setCodePostal('59494')
            ->setVille("Petite-Forêt");
        $user->setRestaurant($restaurant);
        $manager->persist($user);

        $manager->flush();
    }
}
