<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Form\LivreType;
use App\Repository\LivreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[IsGranted('ROLE_AGENT')]
#[Route('/dashboard/livres')]
final class LivreController extends AbstractController
{
    #[Route(name: 'app_livre_index', methods: ['GET'])]
    public function index(LivreRepository $livreRepository): Response
    {
        return $this->render('livre/index.html.twig', [
            'livres' => $livreRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_livre_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        #[Autowire('%covers_directory%')] string $coversDirectory
    ): Response
    {
        $livre = new Livre();
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            /** @var UploadedFile|null $coverFile */
            $coverFile = $form->get('image')->getData();
            if ($coverFile === null && $livre->getImage() === null) {
                $form->addError(new FormError('La couverture est obligatoire.'));
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile|null $coverFile */
            $coverFile = $form->get('image')->getData();
            if ($coverFile instanceof UploadedFile) {
                $originalFilename = pathinfo($coverFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$coverFile->guessExtension();

                try {
                    if (!is_dir($coversDirectory)) {
                        mkdir($coversDirectory, 0775, true);
                    }
                    $coverFile->move($coversDirectory, $newFilename);
                    $livre->setImage($newFilename);
                } catch (FileException $exception) {
                    $form->addError(new FormError('Erreur lors de l\'upload de l\'image.'));
                }
            }

            if ($form->isValid()) {
                $entityManager->persist($livre);
                $entityManager->flush();
                $this->addFlash('success', 'Livre cree avec succes.');

                return $this->redirectToRoute('app_livre_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('livre/new.html.twig', [
            'livre' => $livre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_livre_show', methods: ['GET'])]
    public function show(Livre $livre): Response
    {
        return $this->render('livre/show.html.twig', [
            'livre' => $livre,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_livre_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Livre $livre,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        #[Autowire('%covers_directory%')] string $coversDirectory
    ): Response
    {
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile|null $coverFile */
            $coverFile = $form->get('image')->getData();
            if ($coverFile instanceof UploadedFile) {
                $originalFilename = pathinfo($coverFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$coverFile->guessExtension();

                try {
                    if (!is_dir($coversDirectory)) {
                        mkdir($coversDirectory, 0775, true);
                    }
                    $coverFile->move($coversDirectory, $newFilename);
                    $livre->setImage($newFilename);
                } catch (FileException $exception) {
                    $form->addError(new FormError('Erreur lors de l\'upload de l\'image.'));
                }
            }

            if ($form->isValid()) {
                $entityManager->flush();
                $this->addFlash('success', 'Livre mis a jour.');

                return $this->redirectToRoute('app_livre_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('livre/edit.html.twig', [
            'livre' => $livre,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_livre_delete', methods: ['POST'])]
    public function delete(Request $request, Livre $livre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$livre->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($livre);
            $entityManager->flush();
            $this->addFlash('success', 'Livre supprime.');
        }

        return $this->redirectToRoute('app_livre_index', [], Response::HTTP_SEE_OTHER);
    }
}
