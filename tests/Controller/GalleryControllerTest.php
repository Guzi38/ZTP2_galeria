<?php

/**
 * Gallery controller test.
 */

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class GalleryControllerTest.
 */
class GalleryControllerTest extends WebTestCase
{
    /**
     * Test if the gallery index page is successful.
     */
    public function testGalleryIndexPageIsSuccessful(): void
    {
        $client = static::createClient();
        $client->request('GET', '/gallery');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Galerie');
    }
}
