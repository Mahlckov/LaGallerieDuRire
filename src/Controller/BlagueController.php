<?php

namespace App\Controller;

use App\Entity\Blague;
use App\Form\BlagueFormType;
use App\Repository\BlagueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlagueController extends AbstractController
{
    /**
     * @Route("/blague/new/{id?}", name="nouvelle_blague", requirements={"id"="\d+"})
     */
    public function creerNouvelleBlague(
                        Request                $request,
                        EntityManagerInterface $entityManager
                        ): Response
    {
        $blague = new Blague();
        $form = $this->createForm(BlagueFormType::class, $blague);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $blague->setHumouriste($this->getUser());
            $entityManager->persist($blague);
            $entityManager->flush();
            $this->addFlash('success', 'La blague ' . $blague->getLibelle() . 'a bien été créé!');

            // redirect to the group listing page
            return $this->redirectToRoute('app_list_blagues');
        }

        return $this->render('main/home.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/blague", name="app_list_blagues", methods={"GET"})
     */
    public function listeBlagues(BlagueRepository $blagueRepository)
    {
        $id=$this->getUser()->getId();
        $blagues = $blagueRepository->findBlague($id);


        return $this->render('main/liste_blagues.html.twig', [
            'blagues' => $blagues,
        ]);

    }

    /**
     * @Route("/blague/editer/{id}", name="/blague_editer")
     */
    public function editer(Request $request,
                           EntityManagerInterface $entityManager,
                           BlagueRepository $blagueRepository):Response
    {
        $id = $request->get('id');
        $blague = $blagueRepository->find($id);

        if (!$blague) {
            throw $this->createNotFoundException("Cette blague n'existe pas");
        }

        $form = $this->createForm(BlagueFormType::class, $blague);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($blague);
            $entityManager->flush();

            $this->addFlash('success', 'Groupe ' . $blague->getLibelle() .'modifiée avec succès!');
            return $this->redirectToRoute('app_list_blagues');
        }
        return $this->render('main/editer_blague.html.twig', [
            'blague' => $blague,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/blague/{id}", name="blague_supprimer")
     */
    public function supprimerBlague (
        Request $request,
        EntityManagerInterface $entityManager,
        BlagueRepository $blagueRepository): Response
    {
        $id=$request->get('id');
        $blague=$blagueRepository->find($id);
        $entityManager->remove($blague);
        $entityManager->flush();

        $this->addFlash('success', 'La blague "' . $blague->getLibelle() .'" vient d\'être supprimée.');

        return $this->redirectToRoute('app_list_blagues');
    }
}