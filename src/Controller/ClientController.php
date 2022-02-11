<?php

namespace App\Controller;

use App\Api\VilleApi;
use App\Entity\Client;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_USER")
 * @Route("/client")
 */
class ClientController extends AbstractController
{
    /**
     * @Route("/mon-compte", name="client_index", methods={"GET"})
     */
    public function index(ClientRepository $clientRepository): Response
    {
        $user = $this->getUser();
        if ($user && !$user->getClient()) {
            return $this->redirectToRoute('client_new');
        }

        $client = $clientRepository->findOneBy(array('user'=>$this->getUser()->getId()));
        return $this->render('client/show.html.twig', [
            'client' => $client,
        ]);
    }

    /**
     * @Route("/new", name="client_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $client = new Client();
        $villes = VilleApi::getVilles();
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $client->setUser($user);
            $entityManager->persist($client);
            $entityManager->flush();

            return $this->redirectToRoute('client_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('client/new.html.twig', [
            'client' => $client,
            'form' => $form,
            'villes' => $villes,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="client_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Client $client, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);
        $villes = VilleApi::getVilles();

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('client_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('client/edit.html.twig', [
            'client' => $client,
            'form' => $form,
            'villes' => $villes,
        ]);
    }
}
