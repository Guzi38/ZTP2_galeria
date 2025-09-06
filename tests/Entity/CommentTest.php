<?php

namespace App\Tests\Entity;

use App\Entity\Comment;
use App\Entity\Photo;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

final class CommentTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $c = new Comment();
        $this->assertNull($c->getId());

        // content
        $c->setContent('Super zdjęcie!');
        $this->assertSame('Super zdjęcie!', $c->getContent());

        $now = new \DateTimeImmutable('2025-08-11 10:00:00');
        $c->setDate($now);
        $this->assertSame($now, $c->getDate());

        $p = new Photo();
        $ret = $c->setPhoto($p);
        $this->assertSame($c, $ret);
        $this->assertSame($p, $c->getPhoto());

        $u = new User();
        $ret2 = $c->setAuthor($u);
        $this->assertSame($c, $ret2);
        $this->assertSame($u, $c->getAuthor());
    }
}
