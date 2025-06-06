<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PhotoControllerTest extends WebTestCase
{
    public function testPhotoIndexPageIsSuccessful(): void
    {
        $client = static::createClient();
        $client->request('GET', '/photo');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Zdjęcia');
    }

    public function testPhotoShowRedirectsWhenNotFound(): void
    {
        $client = static::createClient();
        $client->request('GET', '/photo/99999');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testCreatePhotoRequiresLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/photo/create');

        $this->assertResponseRedirects('/login');
    }

    public function testEditPhotoPageAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/photo/1/edit');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'edycja');
    }

    public function testDeletePhotoPageAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/photo/1/delete');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Usuń zdjęcie');
    }
}
