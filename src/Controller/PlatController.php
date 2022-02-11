<?php

namespace App\Controller;

use App\Entity\Plat;
use App\Entity\User;
use App\Form\PlatType;
use App\Repository\PlatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/plat")
 */
class PlatController extends AbstractController
{
    /**
     * @Route("/", name="plat_index", methods={"GET"})
     */
    public function index(PlatRepository $platRepository): Response
    {
        return $this->render('plat/index.html.twig', [
            'plats' => $platRepository->findAll(),
        ]);
    }

    /**
     * @IsGranted("ROLE_RESTAURATEUR")
     * @Route("/new", name="plat_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $plat = new Plat();
        $form = $this->createForm(PlatType::class, $plat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {            
            $user = $this->getUser();
            $prix = $form['prix']->getData();            
            $file = $form['photo']->getData();
            $file->move($this->getParameter('plat_image'),$file->getClientOriginalName());

            $plat->setRestaurant($user->getRestaurant());
            $plat->setPhoto($file->getClientOriginalName());
            $plat->setPrix($prix * 100);
            $entityManager->persist($plat);
            $entityManager->flush();

            return $this->redirectToRoute('restaurant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('plat/new.html.twig', [
            'plat' => $plat,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="plat_show", methods={"GET"})
     */
    public function show(Plat $plat): Response
    {
        return $this->render('plat/show.html.twig', [
            'plat' => $plat,
        ]);
    }

    /**
     * @IsGranted("ROLE_RESTAURATEUR")
     * @Route("/{id}/edit", name="plat_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Plat $plat, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PlatType::class, $plat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['photo']->getData();
            $file->move($this->getParameter('plat_image'),$file->getClientOriginalName());

            $plat->setPhoto($file->getClientOriginalName());
            $entityManager->flush();

            return $this->redirectToRoute('restaurant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('plat/edit.html.twig', [
            'plat' => $plat,
            'form' => $form,
        ]);
    }

    /**
     * @IsGranted("ROLE_RESTAURATEUR")
     * @Route("/{id}", name="plat_delete", methods={"POST"})
     */
    public function delete(Request $request, Plat $plat, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$plat->getId(), $request->request->get('_token'))) {
            $entityManager->remove($plat);
            $entityManager->flush();
        }

        return $this->redirectToRoute('restaurant_index', [], Response::HTTP_SEE_OTHER);
    }
}
