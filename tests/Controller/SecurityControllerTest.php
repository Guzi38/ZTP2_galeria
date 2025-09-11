<?php

/**
 * Security controller test.
 */

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class SecurityControllerTest.
 */
final class SecurityControllerTest extends WebTestCase
{
    /**
     * Test if the login page loads successfully and contains the expected title.
     */
    public function testLoginPageLoads(): void
    {
        $c = static::createClient();
        $c->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertPageTitleContains('Logowanie');
    }
}
