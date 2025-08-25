<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class UserControllerTest extends WebTestCase
{
    public function testIndexLoadsOrRedirects(): void
    {
        $c = static::createClient();
        $c->request('GET', '/user');

        $this->assertTrue(
            $c->getResponse()->isSuccessful() || $c->getResponse()->isRedirection(),
            'Expected 2xx or redirect for /user'
        );
    }
}
