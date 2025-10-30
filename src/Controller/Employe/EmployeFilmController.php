<?php

namespace App\Controller\Employe;

use App\Entity\Film;
use App\Form\FilmType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/employe/films', name: 'employe_films_')]

class EmployeFilmController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $films = $entityManager->getRepository(Film::class)->findAll();
        $s3BaseUrl = 'https://bucketeer-b78e6166-923a-41f5-8eac-7295c143deb0.s3.eu-west-1.amazonaws.com/';

        return $this->render('admin/films/index.html.twig', [
            'films' => $films,
            's3BaseUrl' => $s3BaseUrl,
        ]);
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $film = new Film();
        $form = $this->createForm(FilmType::class, $film);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();

            if ($imageFile) {
                $film->setImageName($imageFile->getClientOriginalName());
                $film->setImageSize($imageFile->getSize());
                $film->setImageFile($imageFile);
            }

            $entityManager->persist($film);
            $entityManager->flush();

            $this->addFlash('success', 'Le film a été ajouté avec succès.');
            return $this->redirectToRoute('employe_films_index');
        }

        return $this->render('admin/films/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'edit')]
    public function edit(Film $film, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FilmType::class, $film);
        $s3BaseUrl = 'https://bucketeer-b78e6166-923a-41f5-8eac-7295c143deb0.s3.eu-west-1.amazonaws.com/';
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $film->setImageName($imageFile->getClientOriginalName());
                $film->setImageSize($imageFile->getSize());
                $film->setImageFile($imageFile);
            }

            $film->getGenres()->clear();
            foreach ($form->get('genres')->getData() as $genre) {
                $film->addGenre($genre);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Le film a été modifié avec succès.');
            return $this->redirectToRoute('employe_films_index');
        }

        return $this->render('admin/films/edit.html.twig', [
            'form' => $form->createView(),
            'film' => $film,
            's3BaseUrl' => $s3BaseUrl,
        ]);
    }

    #[Route('/{id}/delete', name: 'delete')]
    public function delete(Film $film, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($film);
        $entityManager->flush();

        $this->addFlash('success', 'Le film a été supprimé avec succès.');
        return $this->redirectToRoute('employe_films_index');
    }
}
