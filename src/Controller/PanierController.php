<?php

namespace App\Controller;

use App\Repository\PlatRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    /**
     * @Route("/panier", name="afficher_panier")
     * @param SessionInterface $session
     */
    public function validerCommande(SessionInterface $session, PlatRepository $platRepository)
    {

        $panier = $session->get('panier');
        $panierWhithData = [];

        foreach ($panier as $id => $quantite) {
            $panierWhithData[] = [
                'plat' => $platRepository->find($id),
                'quantite' => $quantite
            ];
        }

        return $this->render('panier/index.html.twig', ["plats" => $panierWhithData]);
    }

    /**
     * @Route("/panier/add/{id<\d+>}", name="ajouter_plat")
     * @param $id
     * @param SessionInterface $session
     */
    public function ajouterPlat($id, SessionInterface $session)
    {

        $panier = $session->get('panier', []);

        if (!empty($panier[$id])) {
            $panier[$id]++;
        } else {
            $panier[$id] = 1;
        }

        $session->set('panier', $panier);

        return $this->redirectToRoute('afficher_panier');
    }

    /**
     * @Route("/panier/remove/{id<\d+>}", name="retirer_plat")
     * @param $id
     * @param SessionInterface $session
     */
    public function retirerPlat($id, SessionInterface $session)
    {

        $panier = $session->get('panier', []);

        if (!empty($panier[$id])) {
            unset($panier[$id]);
        }

        $session->set('panier', $panier);

        return $this->redirectToRoute('afficher_panier');
    }
}
