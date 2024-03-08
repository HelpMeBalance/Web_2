<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\EditProfileType;
use App\Form\ChangePasswordFormType;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[Route('/profile')]
class ProfileController extends AbstractController
{
    #[Route(path: '', name: 'profile')]
    public function index(): Response
    {
        // usually you'll want to make sure the user is authenticated first,
        // see "Authorization" below
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // returns your User object, or null if the user is not authenticated
        // use inline documentation to tell your editor your exact User class
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // Call whatever methods you've added to your User class
        // For example, if you added a getFirstName() method, you can use that.
        // return new Response('Well hi there '.$user->getFirstName());
        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'FirstName' => $user->getFirstName(),
            'LastName' => $user->getLastName(),

        ]);
    }

    #[Route(path: '/edit', name: 'profile_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, ParameterBagInterface $params): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $form = $this->createForm(EditProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle file upload
            $profilePictureFile = $form->get('profilePictureFile')->getData(); // Ensure 'profilePictureFile' matches your form field name
            if ($profilePictureFile) {
                $originalFilename = pathinfo($profilePictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $profilePictureFile->guessExtension();

                try {
                    $profilePictureFile->move(
                        $params->get('profile_pictures_directory'), // Make sure this parameter is defined in your services.yaml
                        $newFilename
                    );
                    $user->setProfilePicture($newFilename); // Update the entity with the new filename
                } catch (FileException $e) {
                    // Handle exception if something happens during file upload
                }
            }

            $entityManager->flush();
            $this->addFlash('message', 'Profile updated successfully.');
            return $this->redirectToRoute('app_homeClient');
        }


        return $this->render('profile/edit.html.twig', [
            'form' => $form->createView(),
            'profilePictureUrl' => '/uploads/profile_pictures/' . $user->getProfilePicture(), // Adjust the path as necessary
            'service' => 0,
            'part' => 0,
            'title' => 'Edit Profile',
            'titlepage' => 'Edit Profile -',
        ]);
    }

    #[Route(path: '/change-password', name: 'profile_change_password')]
    public function changePassword(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);
        $passwordForm = $form->get('plainPassword');


        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $passwordForm->getData();
            $user->setPassword(password_hash($newPassword, PASSWORD_DEFAULT));
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('message', 'Mot de passe mis à jour');
            return $this->redirectToRoute('profile');
        }

        return $this->render('profile/change_password.html.twig', [
            'form' => $form->createView(),
            'service' => 0,
            'part' => 0,
            'title' => 'Change Password',
            'titlepage' => 'Change Password -',
        ]);
    }
}
