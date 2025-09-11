<?php

/**
 * Registration controller test.
 */

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class RegistrationControllerTest.
 */
final class RegistrationControllerTest extends WebTestCase
{
    /**
     * Test if the registration page loads correctly and contains required elements.
     */
    public function testRegisterPageLoads(): void
    {
        $client = static::createClient();
        $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
        $this->assertPageTitleContains('rejestracja');
        $this->assertSelectorExists('form[name="user"]');
        $this->assertSelectorExists('#user_email');
    }
}
