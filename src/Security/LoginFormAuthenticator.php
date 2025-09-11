<?php

/**
 * Login form authenticator.
 */

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * Class LoginFormAuthenticator.
 */
class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';
    public const DEFAULT_ROUTE = 'photo_index';

    /**
     * Constructor.
     *
     * @param UserRepository        $userRepository User repository
     * @param UrlGeneratorInterface $urlGenerator   URL generator
     */
    public function __construct(private readonly UserRepository $userRepository, private readonly UrlGeneratorInterface $urlGenerator)
    {
    }

    /**
     * Checks if request is supported.
     *
     * @param Request $request HTTP request
     *
     * @return bool True if request is supported
     */
    public function supports(Request $request): bool
    {
        return 'app_login' === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    /**
     * Authenticates user.
     *
     * @param Request $request HTTP request
     *
     * @return Passport Passport object
     */
    public function authenticate(Request $request): Passport
    {
        $email = mb_strtolower(trim((string) $request->request->get('email', '')));
        $password = (string) $request->request->get('password', '');

        $request->getSession()->set(Security::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge(
                $email,
                fn (string $id) => $this->userRepository->findOneBy(['email' => $id])
            ),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate', (string) $request->request->get('_csrf_token')),
            ]
        );
    }

    /**
     * Handles successful authentication.
     *
     * @param Request        $request      HTTP request
     * @param TokenInterface $token        Security token
     * @param string         $firewallName Firewall name
     *
     * @return Response|null Redirect response or null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate(self::DEFAULT_ROUTE));
    }

    /**
     * Gets login URL.
     *
     * @param Request $request HTTP request
     *
     * @return string Login URL
     */
    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
