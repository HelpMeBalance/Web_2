<?php
namespace App\Security;

use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use App\Entity\User; // Your User entity
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\OAuth2Credentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;

class GoogleAuthenticator extends AbstractAuthenticator
{
    use TargetPathTrait;

    private $clientRegistry;
    private $em;
    private $router;

    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $em, RouterInterface $router)
    {
        $this->clientRegistry = $clientRegistry;
        $this->em = $em;
        $this->router = $router;
    }

    public function supports(Request $request): bool
    {
        // Check if the request is the OAuth callback
        return $request->attributes->get('_route') === 'connect_google_check';
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient('google');
        $accessToken = $client->getAccessToken();
    $googleUser = $client->fetchUserFromToken($accessToken);
    $email = $googleUser->getEmail();

    // Check if the user already exists, otherwise create a new user
    $user = $this->em->getRepository(User::class)->findOneBy(['googleId' => $googleUser->getId()]);
    if (!$user) {
    $user = new User();
    $user->setGoogleId($googleUser->getId());
    $user->setEmail($email);
    $user->setFirstName($googleUser->getFirstName());
    $user->setLastName($googleUser->getLastName());
    // Set a dummy password - not used for OAuth users
    $user->setPassword(bin2hex(random_bytes(20)));
    $this->em->persist($user);
    $this->em->flush();
    }

    return new Passport(new UserBadge($email), new CustomCredentials(function ($credentials, $user) {
        return true;
    }, $accessToken));



    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        // Redirect to some default page
        return new RedirectResponse($this->router->generate('app_homeClient'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $message);

        return new RedirectResponse($this->router->generate('app_login'));
    }
}
