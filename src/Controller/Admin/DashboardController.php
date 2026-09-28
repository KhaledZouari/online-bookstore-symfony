<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\Auteur;
use App\Entity\Editeur;
use App\Entity\Categorie;
use App\Entity\Livre;
use App\Entity\Commande;
use App\Entity\User;
use App\Controller\Admin\LivreCrudController;
use App\Repository\LivreRepository;
use App\Repository\CommandeRepository;
use App\Repository\CategorieRepository;
use App\Repository\AuteurRepository;
use App\Repository\EditeurRepository;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
#[IsGranted('ROLE_ADMIN')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private LivreRepository $livreRepository,
        private CommandeRepository $commandeRepository,
        private CategorieRepository $categorieRepository,
        private AuteurRepository $auteurRepository,
        private EditeurRepository $editeurRepository
    ) {
    }

    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'stats' => [
                'livres' => $this->livreRepository->count([]),
                'commandes' => $this->commandeRepository->count([]),
                'categories' => $this->categorieRepository->count([]),
                'auteurs' => $this->auteurRepository->count([]),
                'editeurs' => $this->editeurRepository->count([]),
            ],
        ]);

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // 1.1) If you have enabled the "pretty URLs" feature:
        // return $this->redirectToRoute('admin_user_index');
        //
        // 1.2) Same example but using the "ugly URLs" that were used in previous EasyAdmin versions:
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirectToRoute('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('MyBookstore');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Auteurs', 'fa fa-pen', Auteur::class);
        yield MenuItem::linkToCrud('Editeurs', 'fa fa-building', Editeur::class);
        yield MenuItem::linkToCrud('Categories', 'fa fa-tags', Categorie::class);
        yield MenuItem::linkToCrud('Livres', 'fa fa-book', Livre::class);
        yield MenuItem::linkToCrud('Commandes', 'fa fa-receipt', Commande::class);
        yield MenuItem::linkToCrud('Utilisateurs', 'fa fa-users', User::class);
    }
}
