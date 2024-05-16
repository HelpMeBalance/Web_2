<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\EditProfileType;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpKernel\KernelInterface;

#[Route('/profile')]
class ProfileController extends AbstractController
{
    private $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    #[Route(path: '', name: 'profile')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        return $this->render('profile/index.html.twig', [
            'user' => $user,
            'FirstName' => $user->getFirstName(),
            'LastName' => $user->getLastName(),
        ]);
    }

    #[Route(path: '/edit', name: 'profile_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $form = $this->createForm(EditProfileType::class, $user);
        $form->handleRequest($request);

        $profilePictureFile = str_replace('\\', '/', $this->stripProjectDir($user->getProfilePicture()));

        if ($form->isSubmitted() && $form->isValid()) {
            $profilePictureFile = $form->get('profilePictureFile')->getData();
            if ($profilePictureFile) {
                $originalFilename = pathinfo($profilePictureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $profilePictureFile->guessExtension();

                try {
                    $projectDir = $this->kernel->getProjectDir();
                    $uploadDirectory = $projectDir . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'profile_pictures';
                    $profilePictureFile->move(
                        $uploadDirectory,
                        $newFilename
                    );
                    $fullPath = $uploadDirectory . DIRECTORY_SEPARATOR . $newFilename;
                    $user->setProfilePicture($fullPath);
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
            'profilePictureUrl' => $profilePictureFile,
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
            return $this->redirectToRoute('app_homeClient');
        }

        return $this->render('profile/change_password.html.twig', [
            'form' => $form->createView(),
            'service' => 0,
            'part' => 0,
            'title' => 'Change Password',
            'titlepage' => 'Change Password -',
        ]);
    }

    private function stripProjectDir(string $fullPath): string
    {
        $projectDir = $this->kernel->getProjectDir() . DIRECTORY_SEPARATOR . 'public';
        return str_replace($projectDir, '', $fullPath);
    }
}
