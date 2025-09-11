<?php

/**
 * Tag controller test.
 */

namespace App\Tests\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class TagControllerTest.
 */
final class TagControllerTest extends WebTestCase
{
    /**
     * Test if the tag index page loads successfully.
     */
    public function testIndexLoads(): void
    {
        $c = $this->createAuthenticatedClient();
        $c->request('GET', '/tag');

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('<body', $c->getResponse()->getContent());
    }

    /**
     * Test if the create tag form loads successfully.
     */
    public function testCreateFormLoads(): void
    {
        $c = $this->createAuthenticatedClient();
        $crawler = $c->request('GET', '/tag/create');

        $this->assertResponseIsSuccessful();
        $this->assertGreaterThan(0, $crawler->filter('form')->count());
    }

    /**
     * Test if submitting invalid data keeps the form visible.
     */
    public function testCreateSubmitInvalidDataKeepsForm(): void
    {
        $c = $this->createAuthenticatedClient();
        $crawler = $c->request('GET', '/tag/create');

        $form = $crawler->filter('form')->form();
        $c->submit($form, []);

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('<form', $c->getResponse()->getContent());
    }

    /**
     * Returns a logged-in client using an admin user from fixtures.
     *
     * @return \Symfony\Bundle\FrameworkBundle\KernelBrowser
     */
    private function createAuthenticatedClient()
    {
        $client = static::createClient();

        /** @var ContainerInterface $container */
        $container = static::getContainer();
        $userRepo = $container->get('doctrine')->getRepository(User::class);

        // Use admin0@example.com from UserFixtures
        $testUser = $userRepo->findOneBy(['email' => 'admin0@example.com']);
        $this->assertNotNull($testUser, 'Test user admin0@example.com not found in fixtures');

        $client->loginUser($testUser);

        return $client;
    }
}
