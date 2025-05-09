<?php

namespace App\Security;

use App\Entity\Tenant;
use App\Entity\User;
use App\Provider\TenantProvider;
use App\Repository\TenantRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\HttpUtils;

class UserAuthenticator extends AbstractLoginFormAuthenticator
{
    public function __construct(
        private readonly HttpUtils $httpUtils,
        private readonly UserRepository $userRepository,
        private readonly TenantRepository $tenantRepository,
        private readonly TenantProvider $tenantProvider,
        private readonly EntityManagerInterface $em,
        private readonly AuthenticationSuccessHandler $authenticationSuccessHandler,
    )
    {
    }

    public function supports(Request $request): bool
    {
        return $request->isMethod('POST')
            && $this->getLoginUrl($request) === $request->getSchemeAndHttpHost().$request->getPathInfo();
    }

    public function authenticate(Request $request): Passport
    {
        $tenant = $this->tenantRepository->find($request->get('tenant'));
        if (null === $tenant) {
            throw new AuthenticationException('Tenant not found.');
        }
        $tenantDto = $tenant->getMcTenant($this->tenantProvider);
        if (null === $tenantDto) {
            throw new AuthenticationException('Tenant configuration not found.');
        }
        $userCandidate = $this->userRepository->findUserWithTenant($request->get('username'), $tenant->getId());
        $isTenantRegistrationPasswordCorrect = $tenant->getRegistrationPassword() === $request->get('password');
        $useTenantRegistrationFlow = ($userCandidate===null || empty($userCandidate->getPassword()))
            && $isTenantRegistrationPasswordCorrect;

        if (!$useTenantRegistrationFlow) {
            $userBadge = new UserBadge(
                $request->get('username'),
                function (string $username) use ($tenant) {
                    return $this->userRepository->findUserWithTenant($username, $tenant->getId());
                },
            );
            $passport = new Passport(
                $userBadge,
                new PasswordCredentials($request->get('password'))
            );
            $passport->addBadge(new CsrfTokenBadge('authenticate', $request->get('_csrf_token')));
            return $passport;
        } elseif ($isTenantRegistrationPasswordCorrect) {
            if ($userCandidate === null) {
                $userCandidate = $this->getOrCreatePlayer($request->get('username'), $tenant);
            }
            $userBadge = new UserBadge(
                $userCandidate->getUsername(),
                function () use ($userCandidate) {
                    return $userCandidate;
                },
            );
            return new SelfValidatingPassport($userBadge);
        } else {
            throw new AuthenticationException('Login failed.');
        }
    }

    private function getOrCreatePlayer(string $username, Tenant $tenant): User
    {
        $user = $this->userRepository->findOneBy(['username' => $username]);
        if (null === $user) {
            $user = new User();
            $user->setUsername($username);
            $this->em->persist($user);
        }
        $user->addRole(User::ROLE_PLAYER);
        $user->addTenant($tenant);
        $this->em->flush();
        return $user;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return $this->authenticationSuccessHandler->onAuthenticationSuccess($request, $token);
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->httpUtils->generateUri($request, 'app_login');
    }
}
