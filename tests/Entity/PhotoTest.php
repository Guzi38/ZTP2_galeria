<?php

namespace App\Tests\Entity;

use App\Entity\Comment;
use App\Entity\Gallery;
use App\Entity\Photo;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class PhotoTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $photo = new Photo();

        $this->assertNull($photo->getId());

        //   $createdAt = new \DateTimeImmutable('2025-08-11 10:00:00');
        //   $photo->setCreatedAt($createdAt);
        //  $this->assertSame($createdAt, $photo->getCreatedAt());

        //  $updatedAt = new \DateTimeImmutable('2025-08-11 12:00:00');
        //   $photo->setUpdatedAt($updatedAt);
        //  $this->assertSame($updatedAt, $photo->getUpdatedAt());

        //   $date = new \DateTimeImmutable('2025-08-11 14:00:00');
        //   $photo->setDate($date);
        //   $this->assertSame($date, $photo->getDate());

        $title = 'My Photo';
        $photo->setTitle($title);
        $this->assertSame($title, $photo->getTitle());

        $content = 'This is a test photo';
        $photo->setContent($content);
        $this->assertSame($content, $photo->getContent());

        $filename = 'photo.jpg';
        $photo->setFilename($filename);
        $this->assertSame($filename, $photo->getFilename());

        $gallery = new Gallery();
        $photo->setGallery($gallery);
        $this->assertSame($gallery, $photo->getGallery());

        $tag = new Tag();
        $this->assertInstanceOf(ArrayCollection::class, $photo->getTags());
        $this->assertCount(0, $photo->getTags());
        $photo->addTag($tag);
        $this->assertCount(1, $photo->getTags());
        $photo->removeTag($tag);
        $this->assertCount(0, $photo->getTags());

        $comment = new Comment();
        $this->assertInstanceOf(ArrayCollection::class, $photo->getComments());
        $this->assertCount(0, $photo->getComments());
        $photo->addComment($comment);
        $this->assertCount(1, $photo->getComments());
        $this->assertSame($photo, $comment->getPhoto());
        $photo->removeComment($comment);
        $this->assertCount(0, $photo->getComments());
        $this->assertNull($comment->getPhoto());

        // Author
        $author = new User();
        $photo->setAuthor($author);
        $this->assertSame($author, $photo->getAuthor());
    }
}
