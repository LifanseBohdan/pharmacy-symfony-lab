<?php

namespace App\Command;

use App\Entity\Category;
use App\Entity\Medicine;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:seed-data',
    description: 'Додає тестові категорії та препарати в базу даних',
)]
class SeedDataCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $existingCount = $this->entityManager->getRepository(Medicine::class)->count([]);
        if ($existingCount > 0) {
            $io->note(sprintf('У базі вже є %d препарат(ів). Сідінг пропущено.', $existingCount));

            return Command::SUCCESS;
        }

        $categoriesData = [
            'painkillers' => [
                'name' => 'Знеболювальні та жарознижувальні',
                'description' => 'Препарати для зменшення болю та зниження температури тіла.',
            ],
            'antibiotics' => [
                'name' => 'Антибіотики',
                'description' => 'Антибактеріальні засоби для лікування бактеріальних інфекцій.',
            ],
            'vitamins' => [
                'name' => 'Вітаміни та мінерали',
                'description' => 'Комплекси для підтримки імунітету та загального тонусу організму.',
            ],
            'cardiology' => [
                'name' => 'Серцево-судинні засоби',
                'description' => 'Препарати для нормалізації артеріального тиску та роботи серця.',
            ],
        ];

        $categories = [];
        foreach ($categoriesData as $key => $data) {
            $category = new Category();
            $category->setName($data['name']);
            $category->setDescription($data['description']);
            $this->entityManager->persist($category);
            $categories[$key] = $category;
        }

        $medicinesData = [
            ['Нурофен Форте 400 мг', 'Reckitt Benckiser', '145.50', 34, false, 'painkillers'],
            ['Парацетамол-Дарниця 500 мг', 'Дарниця', '42.00', 120, false, 'painkillers'],
            ['Амоксиклав 875/125 мг', 'Sandoz', '310.00', 18, true, 'antibiotics'],
            ['Азитроміцин 500 мг', 'Фармак', '128.90', 0, true, 'antibiotics'],
            ['Вітамін D3 2000 МО', 'Now Foods', '265.00', 45, false, 'vitamins'],
            ['Магній B6 Антистрес', 'Sanofi', '220.00', 27, false, 'vitamins'],
            ['Еналаприл 10 мг', 'Дарниця', '58.40', 12, true, 'cardiology'],
        ];

        foreach ($medicinesData as [$name, $manufacturer, $price, $stock, $rx, $catKey]) {
            $medicine = new Medicine();
            $medicine->setName($name);
            $medicine->setManufacturer($manufacturer);
            $medicine->setPrice($price);
            $medicine->setStockQuantity($stock);
            $medicine->setRequiresPrescription($rx);
            $medicine->setCategory($categories[$catKey]);
            $this->entityManager->persist($medicine);
        }

        $this->entityManager->flush();

        $io->success('Додано 4 категорії та 7 тестових препаратів.');

        return Command::SUCCESS;
    }
}
