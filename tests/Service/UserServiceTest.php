<?php

/**
 * Tests for UserService.
 */

namespace App\Tests\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserService;
use Knp\Component\Pager\PaginatorInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class UserServiceTest.
 */
class UserServiceTest extends TestCase
{
    /**
     * Test that findOneBy() returns the correct user.
     */
    public function testFindOneByEmail(): void
    {
        $repo = $this->createMock(UserRepository::class);
        $paginator = $this->createMock(PaginatorInterface::class);
        $hasher = $this->createMock(UserPasswordHasherInterface::class);

        $user = new User();
        $repo->method('findOneBy')->willReturn($user);

        $service = new UserService($repo, $paginator, $hasher);

        $result = $service->findOneBy('test@example.com');

        $this->assertSame($user, $result);
    }

    /**
     * Test that updateProfile() normalizes the email and saves the user.
     */
    public function testUpdateProfileNormalizesEmail(): void
    {
        $repo = $this->createMock(UserRepository::class);
        $repo->expects($this->once())->method('save');

        $paginator = $this->createMock(PaginatorInterface::class);
        $hasher = $this->createMock(UserPasswordHasherInterface::class);

        $service = new UserService($repo, $paginator, $hasher);

        $user = new User();
        $user->setEmail('  TEST@Example.COM  ');

        $service->updateProfile($user);

        $this->assertSame('test@example.com', $user->getEmail());
    }

    /**
     * Test that changePassword() hashes the password and saves the user.
     */
    public function testChangePasswordHashesAndSaves(): void
    {
        $repo = $this->createMock(UserRepository::class);
        $repo->expects($this->once())->method('save');

        $paginator = $this->createMock(PaginatorInterface::class);

        $hasher = $this->createMock(UserPasswordHasherInterface::class);
        $hasher->method('hashPassword')->willReturn('hashed123');

        $service = new UserService($repo, $paginator, $hasher);

        $user = new User();

        $service->changePassword($user, 'plain123');

        $this->assertSame('hashed123', $user->getPassword());
    }
}
