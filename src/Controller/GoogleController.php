<?php

// src/Controller/GoogleController.php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class GoogleController extends AbstractController
{
    #[Route('/connect/google', name: 'connect_google')]
    public function connectAction(ClientRegistry $clientRegistry): RedirectResponse
    {
        return $clientRegistry
            ->getClient('google')
            ->redirect(['email', 'profile'], []);
    }

    #[Route('/connect/google/check', name: 'connect_google_check')]
    public function connectCheckAction(Request $request): RedirectResponse
    {
        // This route will be hit after the user returns from Google.
        // Symfony's Guard authentication will handle the actual login.
    $session = $request->getSession();
    $targetPath = $session->get('_security.main.target_path');

    if (!$targetPath) {
        $targetPath = $this->generateUrl('app_homeClient');
    }


    return $this->redirect($targetPath);
        
        // You can create a Guard authenticator to manage the authentication process.
        // The authenticator will retrieve the user information and create the User object.
    }
}
