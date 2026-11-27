<?php

namespace App\Controller\Admin;

use App\Entity\Medicine;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class MedicineCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Medicine::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Препарат')
            ->setEntityLabelInPlural('Препарати');
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name', 'Назва');
        yield TextField::new('manufacturer', 'Виробник');
        yield NumberField::new('price', 'Ціна (грн)')->setNumDecimals(2);
        yield IntegerField::new('stockQuantity', 'Кількість на складі');
        yield BooleanField::new('requiresPrescription', 'За рецептом');
        yield AssociationField::new('category', 'Категорія');
    }
}
