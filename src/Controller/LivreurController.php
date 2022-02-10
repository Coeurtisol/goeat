<?php

namespace App\Controller;

use App\Api\VilleApi;
use App\Entity\Livreur;
use App\Form\LivreurType;
use App\Repository\CommandeRepository;
use App\Repository\StatutCommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/livreur")
 */
class LivreurController extends AbstractController
{
    /**
     * @IsGranted("ROLE_LIVREUR")
     * @Route("/mon-compte", name="livreur_index", methods={"GET"})
     */
    public function index(CommandeRepository $commandeRepository): Response
    {
        $user = $this->getUser();
        if ($user && !$user->getLivreur()) {
            return $this->redirectToRoute('livreur_new');
        }

        $secteur = $user->getLivreur()->getSecteur();
        $livreur = $user->getLivreur()->getId();
        $commandesEnCours = $commandeRepository->findByStatutBySecteurByLivreur('prise en charge par le livreur', $secteur, $livreur);
        $commandesPretes = $commandeRepository->findByStatutBySecteur('prête', $secteur);
        $commandesLivrees = $commandeRepository->findByStatutByLivreur('livrée', $livreur);

        return $this->render('livreur/index.html.twig', [
            'secteur' => $secteur,
            'commandesEnCours' => $commandesEnCours,
            'commandesPretes' => $commandesPretes,
            'commandesLivrees' => $commandesLivrees,
        ]);
    }

    /**
     * @IsGranted("ROLE_LIVREUR")
     * @Route("/commande/{idCommande}/{action}", name="livreur_action", methods={"GET"})
     */
    public function action(int $idCommande, string $action, EntityManagerInterface $entityManager, CommandeRepository $commandeRepository, StatutCommandeRepository $statutRepository): Response
    {
        $commande = $commandeRepository->find($idCommande);
        $statut = $commande->getStatut();

        if ($action == "prendre") $statut = "prise en charge par le livreur";
        elseif ($action == "terminer") $statut = "livrée";
        else return $this->redirectToRoute('livreur_index');

        $commande->setLivreur($this->getUser()->getLivreur());
        $commande->setStatut($statutRepository->findOneBy(['nom' => $statut]));
        $entityManager->persist($commande);
        $entityManager->flush();

        return $this->redirectToRoute('livreur_index');
    }

    /**
     * @Route("/inscription", name="livreur_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if ($user && $user->getLivreur()) {
            return $this->redirectToRoute('livreur_index');
        }

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
     * @IsGranted("ROLE_LIVREUR")
     * @Route("/modifier-mes-info", name="livreur_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        $livreur = $this->getUser()->getLivreur();
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
}
