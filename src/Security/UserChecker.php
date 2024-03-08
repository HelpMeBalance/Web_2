<?php

// src/Security/UserChecker.php
// src/Security/UserChecker.php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof \App\Entity\User) {
            return;
        }

        // Check if the user is banned
        if ($user->getIsBanned()) {
            // Throw a custom exception
            throw new CustomUserMessageAccountStatusException('Your account has been banned.');
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // Ensure that the user object is the expected entity
        if (!$user instanceof \App\Entity\User) {
            return;
        }

        // Since we're not using isExpired, there's no need to implement anything here.
        // You can add other post-auth checks here if necessary.
    }
}
