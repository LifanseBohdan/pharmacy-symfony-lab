<?php

namespace App\Controller;

use App\Repository\MedicineRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SiteController extends AbstractController
{
    #[Route('/', name: 'site_home', methods: ['GET'])]
    public function home(): Response
    {
        return $this->redirectToRoute('site_medicines_index');
    }

    #[Route('/medicines', name: 'site_medicines_index', methods: ['GET'])]
    public function index(MedicineRepository $medicineRepository): Response
    {
        $medicines = $medicineRepository->findAllWithCategory();

        return $this->render('site/index.html.twig', [
            'medicines' => $medicines,
        ]);
    }

    #[Route('/medicines/{id<\d+>}', name: 'site_medicines_show', methods: ['GET'])]
    public function show(int $id, MedicineRepository $medicineRepository): Response
    {
        $medicine = $medicineRepository->find($id);

        if (!$medicine) {
            throw $this->createNotFoundException('Препарат не знайдено.');
        }

        return $this->render('site/show.html.twig', [
            'medicine' => $medicine,
        ]);
    }
}
