<?php

/**
 * PhotoTest.
 */

namespace App\Tests\Entity;

use App\Entity\Comment;
use App\Entity\Gallery;
use App\Entity\Photo;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

/**
 * Class PhotoTest.
 */
class PhotoTest extends TestCase
{
    /**
     * Test getters and setters of Photo entity.
     */
    public function testGettersAndSetters(): void
    {
        $photo = new Photo();

        $this->assertNull($photo->getId());

        // Title
        $title = 'My Photo';
        $photo->setTitle($title);
        $this->assertSame($title, $photo->getTitle());

        // Content
        $content = 'This is a test photo';
        $photo->setContent($content);
        $this->assertSame($content, $photo->getContent());

        // Filename
        $filename = 'photo.jpg';
        $photo->setFilename($filename);
        $this->assertSame($filename, $photo->getFilename());

        // Gallery
        $gallery = new Gallery();
        $photo->setGallery($gallery);
        $this->assertSame($gallery, $photo->getGallery());

        // Tags
        $tag = new Tag();
        $this->assertInstanceOf(ArrayCollection::class, $photo->getTags());
        $this->assertCount(0, $photo->getTags());
        $photo->addTag($tag);
        $this->assertCount(1, $photo->getTags());
        $photo->removeTag($tag);
        $this->assertCount(0, $photo->getTags());

        // Comments
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
