<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class RegistrationControllerTest extends WebTestCase
{
    public function testRegisterPageLoads(): void
    {
        $client = static::createClient();
        $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();                // 200
        $this->assertPageTitleContains('rejestracja');
        $this->assertSelectorExists('form[name="user"]');   // formularz
        $this->assertSelectorExists('#user_email');         // pole e-mail
    }
}
