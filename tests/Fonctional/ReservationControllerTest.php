<?php

namespace App\Tests\Controller;

use App\Entity\Cinema;
use App\Entity\Film;
use App\Entity\Salle;
use App\Entity\Seance;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ReservationControllerTest extends WebTestCase
{
    public function testReserverSeance(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine')->getManager();
        
        $connection = $entityManager->getConnection();         
        $platform   = $connection->getDatabasePlatform();     

        //  Nettoyage des tables 
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS=0');
        $connection->executeStatement($platform->getTruncateTableSQL('reservation', true));
        $connection->executeStatement($platform->getTruncateTableSQL('seance', true));
        $connection->executeStatement($platform->getTruncateTableSQL('salle', true));
        $connection->executeStatement($platform->getTruncateTableSQL('cinema', true));
        $connection->executeStatement($platform->getTruncateTableSQL('film', true));
        $connection->executeStatement($platform->getTruncateTableSQL('user', true));
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS=1');
        $existingUser = $entityManager->getRepository(User::class)

        ->findOneBy(['email' => 'testuser@example.com']);
        if ($existingUser) {
            $entityManager->remove($existingUser);
            $entityManager->flush();
        }

        //  Création d'un utilisateur de test 
        $user = new User();
        $user->setEmail('testuser@example.com');
        $user->setPassword(password_hash('password123', PASSWORD_BCRYPT));
        $user->setRoles(['ROLE_USER']);
        $entityManager->persist($user);

        //  Création d'un film de test 
        $film = new Film();
        $film->setTitle('Film Test');
        $film->setDescription('Description test');
        $film->setDirector('Réalisateur Test');
        $entityManager->persist($film);



        //  Création d'un cinéma de test 
        $cinema = new Cinema();
        $cinema->setNom('Cinephoria Test');
        $cinema->setVille('Paris'); 
        $entityManager->persist($cinema);
        
        //  Création d'une salle de test 
        $salle = new Salle();
        $salle->setNumero(1);         
        $salle->setCapacite(50);       
        $salle->setQualite('Standard');
        $salle->setCinema($cinema);
        $entityManager->persist($salle);

        //  Création d'une séance future 
        $seance = new Seance();
        $seance->setFilm($film);
        $seance->setSalle($salle);
        $seance->setDateHeureDebut(new \DateTime('+1 day'));
        $seance->setDateHeureFin((new \DateTime('+1 day'))->modify('+2 hours')); // durée de 2h
        $seance->setPlaceDisponible(50);
        $seance->setPrix(10.0);
        $seance->setQualite('Standard');
        $entityManager->persist($seance);

        $entityManager->flush();

        //  Se connecter avec l'utilisateur 
        $client->loginUser($user);

        // Accéder à la page de réservation 
        $crawler = $client->request('GET', '/reservation');

        $this->assertResponseIsSuccessful();

        // Soumettre le formulaire de réservation 
        $form = $crawler->filter('form[name="reservation"]')->form([
            'reservation[seance]' => $seance->getId(),
            'reservation[numPersons]' => 2,
            'reservation[seats]' => json_encode(['A1', 'B2'])
        ]);

        $client->submit($form);

        $this->assertResponseRedirects('/user/reservations');

        //  Vérifier que la réservation est bien en base 
        $reservation = $entityManager
            ->getRepository(\App\Entity\Reservation::class)
            ->findOneBy(['user' => $user, 'seance' => $seance]);

        $this->assertNotNull($reservation);
        $this->assertEquals(2, $reservation->getPlaceReserve());
        $this->assertEquals(20.0, $reservation->getPrix()); 
    }
}
