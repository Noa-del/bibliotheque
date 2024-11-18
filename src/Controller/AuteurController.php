<?php

namespace App\Controller;

use App\Entity\Auteur;
use App\Form\AuteurType;
use App\Repository\AuteurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/auteur')]
final class AuteurController extends AbstractController
{
    #[Route(name: 'app_auteur_index', methods: ['GET'])]
    public function index(AuteurRepository $auteurRepository): Response
    {
        return $this->render('auteur/index.html.twig', [
            'auteurs' => $auteurRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_auteur_new', methods: ['GET', 'POST'])]
    public function new
    (Request $request,
     EntityManagerInterface  $entityManager,
     SluggerInterface $slugger,
     #[Autowire('%kernel.project_dir%/public/uploads/photo')] string $photodirectory
    ): Response
    {
        $auteur = new Auteur();
        $form = $this->createForm(AuteurType::class, $auteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $photofile = $form->get('photo')->getData();
            if ($photofile) {
                $originalFilename = pathinfo($photofile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $auteur->getNom() . '_' . $auteur->getPrenom() . '.' . $safeFilename . '_' . uniqid() . '.' . $photofile->guessExtension();

                try {
                    $photofile->move($photodirectory, $newFilename);
                }
                catch (\Exception $e) {
                    dump($e);
                }

                $auteur->setPhoto($newFilename);
            }
            $entityManager->persist($auteur);
            $entityManager->flush();

            return $this->redirectToRoute('app_auteur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('auteur/new.html.twig', [
            'auteur' => $auteur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_auteur_show', methods: ['GET'])]
    public function show(Auteur $auteur): Response
    {
        return $this->render('auteur/show.html.twig', [
            'auteur' => $auteur,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_auteur_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Auteur $auteur,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger,
        #[Autowire('%kernel.project_dir%/public/uploads/photo')] string $photodirectory

    ): Response
    {
        $form = $this->createForm(AuteurType::class, $auteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $photofile = $form->get('photo')->getData();
            if ($photofile) {
                $originalFilename = pathinfo($photofile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $auteur->getNom() . '_' . $auteur->getPrenom() . '.' . $safeFilename . '_' . uniqid() . '.' . $photofile->guessExtension();

                try {
                    $photofile->move($photodirectory, $newFilename);
                }
                catch (\Exception $e) {
                    dump($e);
                }

                $auteur->setPhoto($newFilename);
            }
            $entityManager->persist($auteur);
            $entityManager->flush();

            return $this->redirectToRoute('app_auteur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('auteur/edit.html.twig', [
            'auteur' => $auteur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_auteur_delete', methods: ['POST'])]
    public function delete(Request $request, Auteur $auteur, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$auteur->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($auteur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_auteur_index', [], Response::HTTP_SEE_OTHER);
    }
}
