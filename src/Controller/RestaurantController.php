<?php

namespace App\Controller;

use App\Api\VilleApi;
use App\Entity\Restaurant;
use App\Form\RestaurantType;
use App\Repository\CommandeRepository;
use App\Repository\StatutCommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/restaurant")
 */
class RestaurantController extends AbstractController
{
    /**
     * @IsGranted("ROLE_RESTAURATEUR")
     * @Route("/mon-compte", name="restaurant_index", methods={"GET"})
     */
    public function index(CommandeRepository $commandeRepository): Response
    {
        $user = $this->getUser();
        if ($user && !$user->getRestaurant()) {
            return $this->redirectToRoute('restaurant_new');
        }

        $restaurant = $user->getRestaurant();
        // $commandes = $commandeRepository->findByStatutBySecteurByLivreur('prise en charge par le livreur', $secteur, $livreur);
        $commandes = $commandeRepository->findBy(['restaurant'=>$restaurant]);

        return $this->render('restaurant/index.html.twig', [
            'restaurant' => $restaurant,
            'commandes' => $commandes
        ]);
    }

    /**
     * @IsGranted("ROLE_RESTAURATEUR")
     * @Route("/commande/{idCommande}/{action}", name="restaurant_action", methods={"GET"})
     */
    public function action(int $idCommande, string $action, EntityManagerInterface $entityManager, CommandeRepository $commandeRepository, StatutCommandeRepository $statutRepository): Response
    {
        $commande = $commandeRepository->find($idCommande);
        $statut = $commande->getStatut();

        if ($action == "accepter") $statut = "acceptée";
        elseif ($action == "finir") $statut = "prête";
        else return $this->redirectToRoute('restaurant_index');

        $commande->setStatut($statutRepository->findOneBy(['nom' => $statut]));
        $entityManager->persist($commande);
        $entityManager->flush();

        return $this->redirectToRoute('restaurant_index');
    }

    /**
     * @IsGranted("ROLE_RESTAURATEUR")
     * @Route("/new", name="restaurant_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $restaurant = new Restaurant();
        $villes = VilleApi::getVilles();
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $restaurant->setProprietaire($user);
            $entityManager->persist($restaurant);
            $entityManager->flush();

            return $this->redirectToRoute('restaurant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('restaurant/new.html.twig', [
            'restaurant' => $restaurant,
            'form' => $form,
            'villes' => $villes,
        ]);
    }

    /**
     * @Route("/{id}", name="restaurant_show", methods={"GET"})
     */
    public function show(Restaurant $restaurant): Response
    {
        return $this->render('restaurant/show.html.twig', [
            'restaurant' => $restaurant,
        ]);
    }

    /**
     * @IsGranted("ROLE_RESTAURATEUR") 
     * @Route("/{id}/edit", name="restaurant_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Restaurant $restaurant, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);
        $villes = VilleApi::getVilles();

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('restaurant_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('restaurant/edit.html.twig', [
            'restaurant' => $restaurant,
            'form' => $form,
            'villes' => $villes,
        ]);
    }
}
