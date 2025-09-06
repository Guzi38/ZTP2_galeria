<?php

namespace App\Tests\Entity\Enum;

use App\Entity\Enum\UserRole;
use PHPUnit\Framework\TestCase;

final class UserRoleTest extends TestCase
{
    public function testCasesExistAndCount(): void
    {
        $cases = UserRole::cases();
        $this->assertCount(2, $cases);
        $this->assertContains(UserRole::ROLE_USER, $cases);
        $this->assertContains(UserRole::ROLE_ADMIN, $cases);
    }

    public function testValues(): void
    {
        $this->assertSame('ROLE_USER', UserRole::ROLE_USER->value);
        $this->assertSame('ROLE_ADMIN', UserRole::ROLE_ADMIN->value);
    }

    public function testFromValue(): void
    {
        $this->assertSame(UserRole::ROLE_USER, UserRole::from('ROLE_USER'));
        $this->assertSame(UserRole::ROLE_ADMIN, UserRole::from('ROLE_ADMIN'));
    }

    public function testLabelMethod(): void
    {
        $this->assertSame('role_user', UserRole::ROLE_USER->label());
        $this->assertSame('role_admin', UserRole::ROLE_ADMIN->label());
    }
}
