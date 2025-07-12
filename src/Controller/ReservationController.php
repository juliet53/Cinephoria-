<?php

namespace App\Controller;

use App\Document\MongoReservation;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\SeanceRepository;
use App\Repository\ReservationRepository;
use DateTime;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Font\OpenSans;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use League\Flysystem\FilesystemOperator;

class ReservationController extends AbstractController
{
    #[Route('/reservation', name: 'app_reservation')]
    public function reserver(
        Request $request,
        SeanceRepository $seanceRepository,
        ReservationRepository $reservationRepository,
        EntityManagerInterface $entityManager,
        DocumentManager $documentManager,
        Security $security,
        FilesystemOperator $defaultStorage
    ): Response {
        $filmFilter = $request->query->get('film', '');
        $dateFilter = $request->query->get('date', '');

        if ($filmFilter || $dateFilter) {
            $seances = $seanceRepository->findByFilters(
                $filmFilter ? urldecode($filmFilter) : null,
                $dateFilter ?: null
            );
        } else {
            $seances = $seanceRepository->findAll();
        }

        $reservedSeatsBySeance = [];
        foreach ($seances as $seance) {
            $reservations = $reservationRepository->findBy(['seance' => $seance]);
            $reservedSeats = [];
            foreach ($reservations as $res) {
                $seats = $res->getSeats();
                if (is_array($seats)) {
                    $reservedSeats = array_merge($reservedSeats, $seats);
                }
            }
            $reservedSeatsBySeance[$seance->getId()] = array_unique($reservedSeats);
        }

        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $seance = $form->get('seance')->getData();
            $selectedSeatsJson = $form->get('seats')->getData();
            $numPersons = $form->get('numPersons')->getData();

            if (!$seance) {
                $this->addFlash('error', 'Séance non trouvée.');
                return $this->redirectToRoute('app_reservation');
            }

            $selectedSeats = json_decode($selectedSeatsJson, true);
            if (!is_array($selectedSeats) || empty($selectedSeats)) {
                $this->addFlash('error', 'Aucune place sélectionnée ou format invalide.');
                return $this->redirectToRoute('app_reservation');
            }

            $placeReserve = count($selectedSeats);
            if ($placeReserve !== $numPersons) {
                $this->addFlash('error', "Le nombre de places sélectionnées ($placeReserve) ne correspond pas au nombre de personnes ($numPersons).");
                return $this->redirectToRoute('app_reservation');
            }

            if ($seance->getPlaceDisponible() < $placeReserve) {
                $this->addFlash('error', 'Pas assez de places disponibles.');
                return $this->redirectToRoute('app_reservation');
            }

            $reservedSeats = $reservedSeatsBySeance[$seance->getId()] ?? [];
            foreach ($selectedSeats as $seat) {
                if (in_array($seat, $reservedSeats)) {
                    $this->addFlash('error', "La place $seat est déjà réservée.");
                    return $this->redirectToRoute('app_reservation');
                }
            }

            $prixUnitaire = $seance->getPrix();
            if ($prixUnitaire === null) {
                $this->addFlash('error', 'Le prix de la séance n\'est pas défini.');
                return $this->redirectToRoute('app_reservation');
            }

            $prixTotal = $prixUnitaire * $placeReserve;

            $reservation->setSeance($seance);
            $reservation->setSeats($selectedSeats);
            $reservation->setPlaceReserve($placeReserve);
            $reservation->setPrix($prixTotal);
            $reservation->setUser($security->getUser());

            $seance->reserverPlaces($placeReserve);

            $qrContent = sprintf(
                "Réservation\nFilm: %s\nDate: %s\nPlaces: %s\nPrix: %.2f€",
                $seance->getFilm()->getTitle(),
                $seance->getDateHeureDebut()->format('Y-m-d H:i'),
                implode(', ', $selectedSeats),
                $prixTotal
            );

            $builder = new Builder(
                writer: new PngWriter(),
                writerOptions: [],
                validateResult: false,
                data: $qrContent,
                encoding: new Encoding('UTF-8'),
                errorCorrectionLevel: ErrorCorrectionLevel::High,
                size: 300,
                margin: 10,
                roundBlockSizeMode: RoundBlockSizeMode::Margin,
                labelText: 'Cinéphoria',
                labelFont: new OpenSans(16),
                labelAlignment: LabelAlignment::Center
            );

            $result = $builder->build();
            $qrCodeFilename = 'qrcodes/qrcode_' . uniqid() . '.png';
            $defaultStorage->write($qrCodeFilename, $result->getString());
            $qrCodeUrl = 'https://bucketeer-b78e6166-923a-41f5-8eac-7295c143deb0.s3.eu-west-1.amazonaws.com/images/Film/' . $qrCodeFilename;
            $reservation->setQrCodePath($qrCodeUrl);

            try {
                $mongoReservation = new MongoReservation();
                $film = $seance->getFilm();
                if ($film && method_exists($film, 'getId')) {
                    $mongoReservation->setFilmId((string) $film->getId());
                } else {
                    $this->addFlash('warning', 'ID du film non trouvé, enregistrement MongoDB ignoré.');
                    throw new \Exception('ID du film non trouvé.');
                }
                $mongoReservation->setReservationDate(new DateTime());
                $mongoReservation->setNumberOfTickets($placeReserve);
                $mongoReservation->setTotalPrice($prixTotal);

                $documentManager->persist($mongoReservation);
                $documentManager->flush();
            } catch (\Exception $e) {
                $this->addFlash('error', 'Échec de l\'enregistrement dans MongoDB : ' . $e->getMessage());
            }

            $entityManager->persist($reservation);
            $entityManager->persist($seance);
            $entityManager->flush();

            $this->addFlash('success', 'Réservation réussie !');
            return $this->redirectToRoute('app_user_reservations');
        }

        return $this->render('reservation/index.html.twig', [
            'form' => $form->createView(),
            'seances' => $seances,
            'reservedSeatsBySeance' => $reservedSeatsBySeance,
            'filmFilter' => $filmFilter,
            'dateFilter' => $dateFilter,
        ]);
    }
}
