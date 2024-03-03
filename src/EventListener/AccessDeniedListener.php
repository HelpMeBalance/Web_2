<?php

// src/EventListener/AccessDeniedListener.php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AccessDeniedListener
{
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function onKernelException(ExceptionEvent $event)
    {
        // Get the exception from the event
        $exception = $event->getThrowable();

        // Check if it's an AccessDeniedException
        if ($exception instanceof AccessDeniedException) {
            // Redirect to the homepage
            $response = new RedirectResponse($this->urlGenerator->generate('app_homeClient')); // Replace 'homepage' with the actual route name of your homepage
            $event->setResponse($response);
        }
    }
        public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

}
