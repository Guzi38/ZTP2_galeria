<?php

/**
 * GalleryTest.
 */

namespace App\Tests\Entity;

use App\Entity\Gallery;
use PHPUnit\Framework\TestCase;

/**
 * Class GalleryTest.
 */
final class GalleryTest extends TestCase
{
    /**
     * Test getters and setters of Gallery entity.
     */
    public function testGettersAndSetters(): void
    {
        $g = new Gallery();

        $this->assertNull($g->getId());
        $this->assertNull($g->getCreatedAt());
        $this->assertNull($g->getUpdatedAt());
        $this->assertNull($g->getTitle());
        $this->assertNull($g->getSlug());

        $created = new \DateTimeImmutable('2025-01-01 10:00:00');
        $updated = new \DateTimeImmutable('2025-01-02 12:00:00');
        $g->setCreatedAt($created);
        $g->setUpdatedAt($updated);
        $this->assertSame($created, $g->getCreatedAt());
        $this->assertSame($updated, $g->getUpdatedAt());

        $g->setTitle('Moja galeria');
        $this->assertSame('Moja galeria', $g->getTitle());

        $ret = $g->setSlug('moja-galeria');
        $this->assertSame($g, $ret);
        $this->assertSame('moja-galeria', $g->getSlug());
    }
}
