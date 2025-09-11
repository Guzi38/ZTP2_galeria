<?php

/**
 * Photo controller test.
 */

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class PhotoControllerTest.
 */
class PhotoControllerTest extends WebTestCase
{
    /**
     * Test if the photo index page loads successfully or redirects.
     */
    public function testPhotoIndexPageLoads(): void
    {
        $client = static::createClient();
        $client->request('GET', '/photo');

        $this->assertTrue(
            $client->getResponse()->isSuccessful() || $client->getResponse()->isRedirection(),
            'Expected 2xx or redirect for /photo'
        );
    }
}
