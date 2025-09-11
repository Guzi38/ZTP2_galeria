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

/**
 * Class UserService.
 */
class UserService implements UserServiceInterface
{
    /**
     * Constructor.
     *
     * @param UserRepository              $userRepository User repository
     * @param PaginatorInterface          $paginator      Paginator service
     * @param UserPasswordHasherInterface $passwordHasher Password hasher
     */
    public function __construct(private readonly UserRepository $userRepository, private readonly PaginatorInterface $paginator, private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    /**
     * Create paginated list of users.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface Paginated list
     */
    public function createPaginatedList(int $page): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->userRepository->queryAll(),
            $page,
            UserRepository::PAGINATOR_ITEMS_PER_PAGE
        );
    }

    /**
     * Update user profile (normalize email).
     *
     * @param User $user User entity
     */
    public function updateProfile(User $user): void
    {
        $normalized = mb_strtolower(trim((string) $user->getEmail()));
        $user->setEmail($normalized);

        $this->userRepository->save($user, true);
    }

    /**
     * Change user password.
     *
     * @param User   $user          User entity
     * @param string $plainPassword Plain password
     */
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

    /**
     * Remove user.
     *
     * @param User $user User entity
     */
    public function remove(User $user): void
    {
        $this->userRepository->remove($user, true);
    }

    /**
     * Find user by email.
     *
     * @param string $email Email address
     *
     * @return User|null User entity or null if not found
     */
    public function findOneBy(string $email): ?User
    {
        return $this->userRepository->findOneBy(['email' => $email]);
    }

    /**
     * Save user (alias for updateProfile).
     *
     * @param User $user User entity
     */
    public function save(User $user): void
    {
        $this->updateProfile($user);
    }

    /**
     * Edit user (alias for updateProfile).
     *
     * @param User $user User entity
     */
    public function edit(User $user): void
    {
        $this->updateProfile($user);
    }
}
