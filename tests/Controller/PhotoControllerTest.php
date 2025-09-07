<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PhotoControllerTest extends WebTestCase
{
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
