<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserCrudController extends AbstractCrudController
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('email')
            ->add('roles')
            ->add('isVerified');
    }

    public function configureFields(string $pageName): iterable
    {
        $readOnly = static fn ($field) => $field->setFormTypeOption('disabled', true);

        $roleField = ChoiceField::new('roles')
            ->setChoices([
                'Abonne' => 'ROLE_ABONNE',
                'Agent' => 'ROLE_AGENT',
                'Admin' => 'ROLE_ADMIN',
            ])
            ->allowMultipleChoices()
            ->renderExpanded();

        $passwordField = TextField::new('plainPassword', 'Nouveau mot de passe')
            ->setFormType(PasswordType::class)
            ->setFormTypeOption('mapped', false)
            ->setFormTypeOption('required', false);

        if ($pageName === Crud::PAGE_NEW) {
            return [
                IdField::new('id')->hideOnForm(),
                EmailField::new('email'),
                $roleField,
                TextField::new('prenom'),
                TextField::new('nom'),
                TextField::new('telephone'),
                TextField::new('adresse'),
                BooleanField::new('isVerified'),
                $passwordField->setFormTypeOption('required', true),
            ];
        }

        if ($pageName === Crud::PAGE_EDIT) {
            return [
                IdField::new('id')->hideOnForm(),
                EmailField::new('email')->setFormTypeOption('disabled', true),
                ArrayField::new('roles')->onlyOnIndex(),
                $roleField,
                $readOnly(TextField::new('prenom')),
                $readOnly(TextField::new('nom')),
                $readOnly(TextField::new('telephone')),
                $readOnly(TextField::new('adresse')),
                $readOnly(BooleanField::new('isVerified')),
                $passwordField,
            ];
        }

        return [
            IdField::new('id'),
            EmailField::new('email'),
            ArrayField::new('roles'),
            TextField::new('prenom'),
            TextField::new('nom'),
            TextField::new('telephone'),
            TextField::new('adresse'),
            BooleanField::new('isVerified'),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof User) {
            parent::persistEntity($entityManager, $entityInstance);
            return;
        }

        $request = $this->getContext()->getRequest();
        $plainPassword = $request->request->all('User')['plainPassword'] ?? null;
        if (is_string($plainPassword) && $plainPassword !== '') {
            $entityInstance->setPassword($this->passwordHasher->hashPassword($entityInstance, $plainPassword));
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof User) {
            parent::updateEntity($entityManager, $entityInstance);
            return;
        }

        $request = $this->getContext()->getRequest();
        $plainPassword = $request->request->all('User')['plainPassword'] ?? null;
        if (is_string($plainPassword) && $plainPassword !== '') {
            $entityInstance->setPassword($this->passwordHasher->hashPassword($entityInstance, $plainPassword));
        }

        parent::updateEntity($entityManager, $entityInstance);
    }
}
