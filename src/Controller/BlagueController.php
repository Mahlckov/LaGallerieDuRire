<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Blague;
use App\Entity\Humouriste;
use App\Form\BlagueFormType;
use App\Repository\BlagueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class BlagueController extends AbstractController
{
    /**
     * @Route("/blague/new/{id?}", name="nouvelle_blague", requirements={"id"="\d+"})
     */
    public function creerNouvelleBlague(
        Request                $request,
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response
    {
        $blague = new Blague();
        $form = $this->createForm(BlagueFormType::class, $blague);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $blague->setHumouriste($this->getUser());
            $blague->setDatePublication(new \DateTime());

            $meme = $form->get('nomMeme')->getData();
            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($meme) {
                $originalFilename = pathinfo($meme->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $meme->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $meme->move(
                        $this->getParameter('memes_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $blague->setNomMeme($newFilename);

                $entityManager->persist($blague);
                $entityManager->flush();
                $this->addFlash('success', 'La blague ' . $blague->getLibelle() . 'a bien été créé!');

                // redirect to the group listing page
                return $this->redirectToRoute('main_home');
            }
        }

        // render the new blague form template with form errors
        return $this->render('blague/newBlague.twig', [
            'form' => $form->createView(),
        ]);
    }



//    /**
//     * @Route("/blague", name="list_blagues", methods={"GET"})
//     */
//    public function listeBlagues(Request $request,BlagueRepository $blagueRepository)
//    :response{
//        //Creation du formulaire de la barre de recherche
//        $search = new SearchData();
//        $searchForm = $this->createForm(SearchForm::class,$search);
//        $searchForm->handleRequest($request);
//
//        //Recherche des blagues (si critères => dans variable search)
//        $listeBlagues=$blagueRepository->findBlagues($search);
//
//
//        return $this->render('main/home.html.twig', [
//            'blagues' => $listeBlagues,
//            'SearchForm' => $searchForm->createView()]);
//    }

    /**
     * @Route("/blague/editer/{id}", name="blague_editer", requirements={"id"="\d+"})
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
            return $this->redirectToRoute('main_home');
        }
        return $this->render('main/editer_blague.html.twig', [
            'blague' => $blague,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/blague/{id}", name="blague_supprimer", requirements={"id"="\d+"})
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

        return $this->redirectToRoute('main_home');
    }
}