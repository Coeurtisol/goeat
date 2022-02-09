<?php

namespace App\Controller;

use App\Api\VilleApi;
use App\Entity\Livreur;
use App\Form\LivreurType;
use App\Repository\LivreurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/livreur")
 */
class LivreurController extends AbstractController
{
    /**
     * @Route("/", name="livreur_index", methods={"GET"})
     */
    public function index(LivreurRepository $livreurRepository): Response
    {
        $user = $this->getUser();
        if ($user && !$user->getLivreur()) {
            return $this->redirectToRoute('livreur_new');
        }

        return $this->render('livreur/index.html.twig', [
            'livreurs' => $livreurRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="livreur_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $livreur = new Livreur();
        $villes = VilleApi::getVilles();
        $form = $this->createForm(LivreurType::class, $livreur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $livreur->setUser($user);
            $entityManager->persist($livreur);
            $entityManager->flush();

            return $this->redirectToRoute('livreur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('livreur/new.html.twig', [
            'livreur' => $livreur,
            'form' => $form,
            'villes' => $villes,
        ]);
    }

    /**
     * @Route("/{id}", name="livreur_show", methods={"GET"})
     */
    public function show(Livreur $livreur): Response
    {
        return $this->render('livreur/show.html.twig', [
            'livreur' => $livreur,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="livreur_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Livreur $livreur, EntityManagerInterface $entityManager): Response
    {
        $villes = VilleApi::getVilles();
        $form = $this->createForm(LivreurType::class, $livreur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('livreur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('livreur/edit.html.twig', [
            'livreur' => $livreur,
            'form' => $form,
            'villes' => $villes,
        ]);
    }

    /**
     * @Route("/{id}", name="livreur_delete", methods={"POST"})
     */
    public function delete(Request $request, Livreur $livreur, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $livreur->getId(), $request->request->get('_token'))) {
            $entityManager->remove($livreur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('livreur_index', [], Response::HTTP_SEE_OTHER);
    }
}
