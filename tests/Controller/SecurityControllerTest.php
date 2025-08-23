<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class SecurityControllerTest extends WebTestCase
{
    public function testLoginPageLoads(): void
    {
        $c = static::createClient();
        $c->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertPageTitleContains('Logowanie');
    }
}
