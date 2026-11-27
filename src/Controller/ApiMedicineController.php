<?php

namespace App\Controller;

use App\Entity\Medicine;
use App\Repository\MedicineRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/medicines', name: 'api_medicines_')]
class ApiMedicineController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(Request $request, MedicineRepository $medicineRepository): JsonResponse
    {
        $inStockParam = $request->query->get('inStock');
        $inStockOnly = $inStockParam === '1' || $inStockParam === 'true';

        $medicines = $medicineRepository->findAllWithCategory($inStockOnly ? true : null);

        $data = array_map(fn (Medicine $medicine): array => $this->serializeMedicine($medicine), $medicines);

        return $this->json($data, Response::HTTP_OK);
    }

    #[Route('/{id<\d+>}', name: 'show', methods: ['GET'])]
    public function show(int $id, MedicineRepository $medicineRepository): JsonResponse
    {
        $medicine = $medicineRepository->find($id);

        if (!$medicine) {
            return $this->json(
                ['error' => sprintf('Medicine with id %d not found.', $id)],
                Response::HTTP_NOT_FOUND
            );
        }

        return $this->json($this->serializeMedicine($medicine), Response::HTTP_OK);
    }

    private function serializeMedicine(Medicine $medicine): array
    {
        $category = $medicine->getCategory();

        return [
            'id' => $medicine->getId(),
            'name' => $medicine->getName(),
            'manufacturer' => $medicine->getManufacturer(),
            'price' => (float) $medicine->getPrice(),
            'stockQuantity' => $medicine->getStockQuantity(),
            'requiresPrescription' => $medicine->isRequiresPrescription(),
            'category' => $category ? [
                'id' => $category->getId(),
                'name' => $category->getName(),
                'description' => $category->getDescription(),
            ] : null,
        ];
    }
}
