<?php

namespace App\Service;

use App\Entity\User;
use Knp\Component\Pager\Pagination\PaginationInterface;

interface UserServiceInterface
{
    public function createPaginatedList(int $page): PaginationInterface;

    public function save(User $user): void;

    public function edit(User $user): void;

    public function remove(User $user): void;

    public function findOneBy(string $email): ?User;

    public function updateProfile(User $user): void;

    public function changePassword(User $user, string $plainPassword): void;
}
