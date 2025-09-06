<?php

namespace App\Tests\Entity;

use App\Entity\Enum\UserRole;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $user = new User();

        $this->assertNull($user->getId());

        $email = 'test@example.com';
        $user->setEmail($email);
        $this->assertSame($email, $user->getEmail());
        $this->assertSame($email, $user->getUserIdentifier());
        $this->assertSame($email, $user->getUsername());

        $login = 'testlogin';
        $user->setLogin($login);
        $this->assertSame($login, $user->getLogin());

        $roles = ['ROLE_ADMIN'];
        $user->setRoles($roles);
        $this->assertContains(UserRole::ROLE_USER->value, $user->getRoles());
        $this->assertContains('ROLE_ADMIN', $user->getRoles());

        $password = 'secret';
        $user->setPassword($password);
        $this->assertSame($password, $user->getPassword());

        $this->assertNull($user->getSalt());

        $this->assertInstanceOf(ArrayCollection::class, $user->getComments());
        $this->assertCount(0, $user->getComments());

        $author = new User();
        $user->setAuthor($author);
        $this->assertSame($author, $user->getAuthor());

        $user->eraseCredentials();
        $this->assertTrue(true);
    }
}
