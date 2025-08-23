<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class TagControllerTest extends WebTestCase
{
    public function testIndexLoads(): void
    {
        $c = static::createClient();
        $c->request('GET', '/tag');

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('<body', $c->getResponse()->getContent());
    }

    public function testCreateFormLoads(): void
    {
        $c = static::createClient();
        $crawler = $c->request('GET', '/tag/create');

        $this->assertResponseIsSuccessful();
        $this->assertGreaterThan(0, $crawler->filter('form')->count());
    }

    public function testCreateSubmitInvalidDataKeepsForm(): void
    {
        $c = static::createClient();
        $crawler = $c->request('GET', '/tag/create');

        $form = $crawler->filter('form')->form();
        $c->submit($form, []);

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('<form', $c->getResponse()->getContent());
    }
}
