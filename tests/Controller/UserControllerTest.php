<?php

/**
 * User controller test.
 */

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class UserControllerTest.
 */
final class UserControllerTest extends WebTestCase
{
    /**
     * Test if the user index page loads or redirects.
     */
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
