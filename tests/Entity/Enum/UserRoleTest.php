<?php

/**
 * UserRoleTest.
 */

namespace App\Tests\Entity\Enum;

use App\Entity\Enum\UserRole;
use PHPUnit\Framework\TestCase;

/**
 * Class UserRoleTest.
 */
final class UserRoleTest extends TestCase
{
    /**
     * Test if UserRole enum contains expected cases and count.
     */
    public function testCasesExistAndCount(): void
    {
        $cases = UserRole::cases();
        $this->assertCount(2, $cases);
        $this->assertContains(UserRole::ROLE_USER, $cases);
        $this->assertContains(UserRole::ROLE_ADMIN, $cases);
    }

    /**
     * Test values of UserRole enum.
     */
    public function testValues(): void
    {
        $this->assertSame('ROLE_USER', UserRole::ROLE_USER->value);
        $this->assertSame('ROLE_ADMIN', UserRole::ROLE_ADMIN->value);
    }

    /**
     * Test from() method of UserRole enum.
     */
    public function testFromValue(): void
    {
        $this->assertSame(UserRole::ROLE_USER, UserRole::from('ROLE_USER'));
        $this->assertSame(UserRole::ROLE_ADMIN, UserRole::from('ROLE_ADMIN'));
    }

    /**
     * Test label() method of UserRole enum.
     */
    public function testLabelMethod(): void
    {
        $this->assertSame('role_user', UserRole::ROLE_USER->label());
        $this->assertSame('role_admin', UserRole::ROLE_ADMIN->label());
    }
}
