<?php

namespace App\Controller;

use App\Entity\Humouriste;
use App\Form\ProfilChangePasswordFormType;
use App\Form\ProfilFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\String\Slugger\SluggerInterface;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main_home", methods={"GET"})
     */
    public function index(): Response {

        return $this->render('main/home.html.twig');
        }

    /**
     * @Route("/profil/{id}", name="main_profil", requirements={"id"="\d+"})
     */
    public function profil(Request $request,
                           Security $security,
                           EntityManagerInterface $entityManager,
                           SluggerInterface $slugger,
                           UserPasswordHasherInterface $userPasswordHasher,
                           int $id
    ): Response
    {
        $humouriste = $security->getUser();

        // Vérifie si l'utilisateur connecté ≠ profil sélectionné
        if ($id !== $humouriste->getId()) {
            // Récupère l'id de l'autre profil
            $humouriste = $entityManager->getRepository(Humouriste::class)->find($id);
            if (!$humouriste) {
                throw $this->createNotFoundException("Cet humouriste n'existe pas");
            }
        }

        $humouristeForm = $this->createForm(ProfilFormType::class, $humouriste);
        $mdpForm = $this->createForm(ProfilChangePasswordFormType::class, $humouriste);
        $humouristeForm->handleRequest($request);
        $mdpForm->handleRequest($request);

        if ($humouristeForm->isSubmitted() && $humouristeForm->isValid()  ) {
            // Vérifie si le mot de passe fourni correspond au mot de passe actuel de l'utilisateur
            if (!$userPasswordHasher->isPasswordValid($humouriste,$humouristeForm->get('plainPassword')->getData())) {
                $this->addFlash('authentication_error', 'Le mot de passe est incorrect.');
                return $this->redirectToRoute('main_profil', ['id' => $humouriste->getId()]);
            }else {
                $image = $humouristeForm->get('nomImage')->getData();
                // this condition is needed because the 'brochure' field is not required
                // so the PDF file must be processed only when a file is uploaded
                if ($image) {
                    $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();

                    // Move the file to the directory where brochures are stored
                    try {
                        $image->move(
                            $this->getParameter('images_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
                    $user = new Humouriste();
                    if ($humouriste instanceof $user) {
                        $humouriste->setNomImage($newFilename);
                    }
                }

                $entityManager->persist($humouriste);
                $entityManager->flush();

                $this->addFlash('success', 'Votre profil a bien été modifié!');
                return $this->redirectToRoute('main_profil', ['id' => $humouriste->getId()]);
            }
            //Cas de soumission du formulaire de modification du mot de passe
        } elseif ($mdpForm->isSubmitted() && $mdpForm->isValid()) {
            if ($userPasswordHasher->isPasswordValid($humouriste, $mdpForm->get('currentPassword')->getData())) {
                $humouriste->setPassword(
                    $userPasswordHasher->hashPassword(
                        $humouriste,
                        $mdpForm->get('newPassword')->getData()
                    )
                );

                $this->addFlash('success', 'Le mot de passe a été modifié.');
                $entityManager->persist($humouriste);
                $entityManager->flush();
                return $this->redirectToRoute('main_profil', ['id' => $humouriste->getId()]);

            } else {
                $this->addFlash('authentication_error', 'Le mot de passe renseigné est incorrect.');

            }
        }

        return $this->render('main/profil.html.twig', [
            'ProfileForm' => $humouristeForm->createView(),
            'MdpForm' => $mdpForm ->createView(),
            'humouriste' => $humouriste]);
    }
}
