<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class RegistrationControllerTest extends WebTestCase
{
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
