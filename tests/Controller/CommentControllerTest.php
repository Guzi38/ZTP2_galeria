<?php

/**
 * CommentController test.
 *
 * PHP version 8.2
 *
 * @category Tests
 */

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class CommentControllerTest.
 *
 * Tests for CommentController routes.
 */
class CommentControllerTest extends WebTestCase
{
    /**
     * Test if comment index page is successful and contains expected heading.
     */
    public function testCommentIndexPageIsSuccessful(): void
    {
        $client = static::createClient();
        $client->request('GET', '/comment');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Komentarze do zdjęć');
    }
}
