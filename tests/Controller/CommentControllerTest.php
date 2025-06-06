<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CommentControllerTest extends WebTestCase
{
    public function testCommentIndexPageIsSuccessful(): void
    {
        $client = static::createClient();
        $client->request('GET', '/comment');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Komentarze do zdjęć');
    }
}
