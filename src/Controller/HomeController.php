<?php

namespace App\Controller;

use App\Repository\FilmRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(FilmRepository $filmRepository): Response
    {
        $films = $filmRepository->findFilmsAddedLastWednesday();
        $s3BaseUrl = 'https://bucketeer-b78e6166-923a-41f5-8eac-7295c143deb0.s3.eu-west-1.amazonaws.com/';

        return $this->render('home/index.html.twig', [
            'films' => $films,
            's3BaseUrl' => $s3BaseUrl,
        ]);
    }
}
