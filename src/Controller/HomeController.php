<?php

namespace App\Controller;

use App\Repository\RestaurantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(RestaurantRepository $restaurantRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'restaurants' => $restaurantRepository->findAll(),
        ]);
    }

    /**
     * @Route("/accueil", name="accueil")
     */
    public function accueil(): Response
    {
        $user = $this->getUser();
        if ($user && $user->getRoles()[0] == "ROLE_RESTAURATEUR") {
            return $this->redirectToRoute('restaurant_index');
        }
        if ($user && $user->getRoles()[0] == "ROLE_LIVREUR") {
            return $this->redirectToRoute('livreur_index');
        }

        return $this->redirectToRoute('home');
    }
}
