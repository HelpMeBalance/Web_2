<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use App\Repository\PublicationRepository;

class DashboardController extends AbstractController
{
    #[Route('/admin', name: 'admin')]
    public function index(UserRepository $userRepository,PublicationRepository $publicationRepository): Response
    {
        // return parent::index();


        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
        $patientCount = $userRepository->countByRole('ROLE_USER');
        $users = $userRepository->findAll();
        $user = $this->getUser();
        $totalPublications = count($publicationRepository->findAll());
        // $usersStats = $userRepository->countUsersByMonthAndRole();


        return $this->render('admin/dashboard.html.twig', [
            'title' => 'Welcome to HelpMeBalance',
            'titlepage' => 'Dashboard',
            'patientCount' => $patientCount,
            'users' => $users,
            'profilePictureUrl' => '/uploads/profile_pictures/' . $user->getProfilePicture(),
            'totalPublications' => $totalPublications,
            // 'usersStats' => $usersStats,
        ]);

    }

}
