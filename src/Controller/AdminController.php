<?php

namespace App\Controller;


use App\Data\SearchData;
use App\Entity\Humouriste;
use App\Form\SearchForm;
use App\Repository\HumouristeRepository;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin", name="admin_",methods={"GET"})
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/gestionHumouristes", name="gestionHumouristes")
     */
    public function gestionHumouristes(
        Request $request,
        HumouristeRepository $humouristeRepository
    ):response{

//Creation du formulaire de la barre de recherche
        $search = new SearchData();
        $searchForm = $this->createForm(SearchForm::class,$search);
        $searchForm->handleRequest($request);

//Recherche des humouristes (si critères => dans variable search)
        $listeHumouristes=$humouristeRepository->findHumouristes($search);


        return $this->render('admin/gestion_humouristes.html.twig',[
            'participants'=>$listeHumouristes,
            'SearchForm' => $searchForm->createView()]);

    }

    /**
     * @Route("/desactivation/{id}", name="desactivation", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function desactiverHumouriste (EntityManagerInterface $entityManager, int $id): Response
    {
        $humouriste = $entityManager->getRepository(Humouriste::class)->find($id);


        if ($humouriste->isActif()) {
            $humouriste->setActif(false);

            $entityManager->persist($humouriste);
            $entityManager->flush();

            $this->addFlash('success', 'Le compte a été désactivé.');
        } else {
            $this->addFlash('error', 'Le compte est déjà inactif.');
        }

        return $this->redirectToRoute('admin_gestionHumouristes');
    }

    /**
     * @Route("/reactivation/{id}", name="reactivation", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function reactiverHumouriste (EntityManagerInterface $entityManager, int $id): Response
    {
        $humouriste = $entityManager->getRepository(Humouriste::class)->find($id);

        $humouriste->setActif(true);

        $entityManager->persist($humouriste);
        $entityManager->flush();

        $this->addFlash('success', 'Le compte a été réactivé.');

        return $this->redirectToRoute('admin_gestionHumouristes');
    }

    /**
     * @Route("/suppression/{id}", name="suppression")
     */
    public function supprimerHumouriste (
        Request $request,
        EntityManagerInterface $entityManager,
        HumouristeRepository $humouristeRepository){

        $id=$request->get('id');
        $humouriste=$humouristeRepository->find($id);
        $entityManager->remove($humouriste);
        $entityManager->flush();

        $this->addFlash('success', 'Le compte a été supprimé.');

        return $this->redirectToRoute('admin_gestionHumouristes');
    }
}
