<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GalleryControllerTest extends WebTestCase
{
    public function testGalleryIndexPageIsSuccessful(): void
    {
        $client = static::createClient();
        $client->request('GET', '/gallery');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Galerie'); // Upewnij się, że tłumaczenie show_galleries: Galerie istnieje
    }
}
