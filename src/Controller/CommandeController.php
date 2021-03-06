<?php

namespace App\Controller;

use App\Api\VilleApi;
use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Form\CommandeType;
use App\Repository\CommandeRepository;
use App\Repository\PlatRepository;
use App\Repository\RestaurantRepository;
use App\Repository\StatutCommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_USER")
 * @Route("/commande")
 */
class CommandeController extends AbstractController
{
    /**
     * @Route("/", name="commande_index", methods={"GET"})
     */
    public function index(CommandeRepository $commandeRepository): Response
    {
        return $this->render('commande/index.html.twig', [
            'commandes' => $commandeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/valider", name="commande_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager, SessionInterface $session, PlatRepository $platRepository, RestaurantRepository $restaurantRepository, StatutCommandeRepository $statutRepository): Response
    {
        $client = $this->getUser()->getClient();
        $villes = VilleApi::getVilles();
        $commande = new Commande();

        if(!$session->has('panier'))
        {
            $session->set('panier', []);
        }

        $panier = $session->get('panier');
        $panierWhithData = [];

        foreach ($panier as $id => $quantite) {
            $panierWhithData[] = [
                'plat' => $platRepository->find($id),
                'quantite' => $quantite
            ];
        }

        $total=300;

        foreach ($panierWhithData as $item) {
            $totalByPlat=$item['plat']->getPrix() * $item['quantite'];
            $total+= $totalByPlat;
        }

        $form = $this->createForm(CommandeType::class, $commande);
        $form->handleRequest($request);

        $restaurant = $restaurantRepository->find($panierWhithData[0]['plat']->getRestaurant()->getId());
        
        if ($form->isSubmitted() && $form->isValid() && $form->getData()->getVille() == $restaurant->getVille()) {
            $commande->setNumero(rand(0, 1000000));
            $commande->setMontant($total);
            $commande->setClient($client);
            $commande->setDate(new \DateTime());
            $commande->setRestaurant($restaurant);
            $commande->setStatut($statutRepository->findOneBy(['nom'=>'en attente']));

            $entityManager->persist($commande);
            $entityManager->flush();
            foreach ($panierWhithData as $plat) {
                $ligneCommande = new LigneCommande();
                $ligneCommande->setCommande($commande);
                $ligneCommande->setPlat($plat['plat']);
                $ligneCommande->setQuantite($plat['quantite']);
                $plat['plat']->setStock($plat['plat']->getStock()-$plat['quantite']);

                $entityManager->persist($ligneCommande);
                $entityManager->flush();
            }

            $session->set('panier', []);

            return $this->redirectToRoute('commande_merci', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commande/new.html.twig', [
            'commande' => $commande,
            'form' => $form,
            'client' => $client,
            'villes' =>$villes
        ]);
    }

    /**
     * @Route("/{id<\d+>}", name="commande_show", methods={"GET"})
     */
    public function show(Commande $commande): Response
    {
        return $this->render('commande/show.html.twig', [
            'commande' => $commande,
        ]);
    }

    /**
     * @Route("/merci", name="commande_merci", methods={"GET"})
     */
    public function merci(CommandeRepository $commandeRepository): Response
    {
        return $this->render('commande/merci.html.twig', [
            'commandes' => $commandeRepository->findAll(),
        ]);
    }
}
