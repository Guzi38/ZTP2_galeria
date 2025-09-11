<?php

/**
 * User service interface.
 */

namespace App\Service;

use App\Entity\User;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Interface UserServiceInterface.
 *
 * Defines methods for managing User entities.
 */
interface UserServiceInterface
{
    /**
     * Create a paginated list of users.
     *
     * @param int $page Page number
     *
     * @return PaginationInterface Paginated list of users
     */
    public function createPaginatedList(int $page): PaginationInterface;

    /**
     * Save a user.
     *
     * @param User $user User entity
     */
    public function save(User $user): void;

    /**
     * Edit a user.
     *
     * @param User $user User entity
     */
    public function edit(User $user): void;

    /**
     * Remove a user.
     *
     * @param User $user User entity
     */
    public function remove(User $user): void;

    /**
     * Find a user by email.
     *
     * @param string $email User email
     *
     * @return User|null User entity or null if not found
     */
    public function findOneBy(string $email): ?User;

    /**
     * Update a user profile.
     *
     * @param User $user User entity
     */
    public function updateProfile(User $user): void;

    /**
     * Change a user's password.
     *
     * @param User   $user          User entity
     * @param string $plainPassword Plain text password
     */
    public function changePassword(User $user, string $plainPassword): void;
}
