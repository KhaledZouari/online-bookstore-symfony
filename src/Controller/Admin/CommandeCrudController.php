<?php

namespace App\Controller\Admin;

use App\Entity\Commande;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CommandeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Commande::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('statut')
            ->add('user');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            ChoiceField::new('statut')
                ->setChoices([
                    'En attente' => 'EN_ATTENTE',
                    'Confirmee' => 'CONFIRMEE',
                    'Livree' => 'LIVREE',
                    'Refusee' => 'REFUSEE',
                ]),
            TextField::new('total')->onlyOnIndex(),
            TextField::new('total')->onlyOnDetail(),
            AssociationField::new('user')->onlyOnIndex(),
            AssociationField::new('user')->onlyOnDetail(),
            AssociationField::new('lignesCommandes')->onlyOnDetail(),
        ];
    }
}
