<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

        return $this->redirect($adminUrlGenerator->setController(MedicineCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Аптека — Адмін-панель');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Головна', 'fa fa-home');
        yield MenuItem::linkTo(CategoryCrudController::class, 'Категорії', 'fas fa-tags');
        yield MenuItem::linkTo(MedicineCrudController::class, 'Препарати', 'fas fa-pills');
        yield MenuItem::linkToRoute('На сайт', 'fas fa-arrow-left', 'site_medicines_index');
    }
}
