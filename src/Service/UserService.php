<?php

/**
 * User service.
 */

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserService implements UserServiceInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly PaginatorInterface $paginator,
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function createPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->userRepository->queryAll(),
            $page,
            UserRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    public function updateProfile(User $user): void
    {
        $normalized = mb_strtolower(trim((string) $user->getEmail()));
        $user->setEmail($normalized);

        $this->userRepository->save($user, true);
    }

    public function changePassword(User $user, string $plainPassword): void
    {
        $plainPassword = trim($plainPassword);
        if ('' === $plainPassword) {
            return;
        }

        $hash = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hash);

        $this->userRepository->save($user, true);
    }

    public function remove(User $user): void
    {
        $this->userRepository->remove($user, true);
    }

    public function findOneBy(string $email): ?User
    {
        return $this->userRepository->findOneBy(['email' => $email]);
    }

    public function save(User $user): void
    {
        $this->updateProfile($user);
    }

    public function edit(User $user): void
    {
        $this->updateProfile($user);
    }
}
