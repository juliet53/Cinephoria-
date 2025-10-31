<?php

namespace App\Controller\Employe;

use App\Entity\Avis;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/employe/avis', name: 'employe_avis_')]

final class EmployeAvisController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $avis = $entityManager->getRepository(Avis::class)->findBy([], ['id' => 'DESC']);

        return $this->render('admin/admin_avis/index.html.twig', [
            'avis' => $avis,
        ]);
    }

    #[Route('/valider/{id}', name: 'valider')]
    public function valider(Avis $avis, EntityManagerInterface $entityManager): Response
    {
        $avis->setValide(true);
        $entityManager->persist($avis);
        $entityManager->flush();

        $this->addFlash('success', 'L\'avis a été validé avec succès.');

        return $this->redirectToRoute('employe_avis_index');
    }

    #[Route('/invalider/{id}', name: 'invalider')]
    public function invalider(Avis $avis, EntityManagerInterface $entityManager): Response
    {
        $avis->setValide(false);
        $entityManager->persist($avis);
        $entityManager->flush();

        $this->addFlash('success', 'L\'avis a été invalidé avec succès.');

        return $this->redirectToRoute('employe_avis_index');
    }
}
