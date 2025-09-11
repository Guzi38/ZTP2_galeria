<?php

/**
 * TagTest.
 */

namespace App\Tests\Entity;

use App\Entity\Tag;
use PHPUnit\Framework\TestCase;

/**
 * Class TagTest.
 */
final class TagTest extends TestCase
{
    /**
     * Test getters and setters of Tag entity.
     */
    public function testGettersAndSetters(): void
    {
        $tag = new Tag();

        $this->assertNull($tag->getId());

        $created = new \DateTimeImmutable('2025-01-01 12:00:00');
        $updated = new \DateTimeImmutable('2025-01-02 13:00:00');

        $tag->setCreatedAt($created);
        $tag->setUpdatedAt($updated);

        $this->assertSame($created, $tag->getCreatedAt());
        $this->assertSame($updated, $tag->getUpdatedAt());

        $tag->setTitle('Krajobraz');
        $tag->setSlug('krajobraz');

        $this->assertSame('Krajobraz', $tag->getTitle());
        $this->assertSame('krajobraz', $tag->getSlug());
    }
}
