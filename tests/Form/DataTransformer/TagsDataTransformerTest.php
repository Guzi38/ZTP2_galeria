<?php

/**
 * Tests for TagsDataTransformer.
 */

namespace App\Tests\Form\DataTransformer;

use App\Entity\Tag;
use App\Form\DataTransformer\TagsDataTransformer;
use App\Service\TagServiceInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

/**
 * Class TagsDataTransformerTest.
 */
class TagsDataTransformerTest extends TestCase
{
    /**
     * Test that an empty collection is transformed into an empty string.
     */
    public function testTransformEmptyCollectionReturnsEmptyString(): void
    {
        $tagService = $this->createMock(TagServiceInterface::class);
        $transformer = new TagsDataTransformer($tagService);

        $out = $transformer->transform(new ArrayCollection());
        $this->assertSame('', $out);
    }

    /**
     * Test that tags are transformed into a comma-separated string.
     */
    public function testTransformReturnsCommaSeparatedTitles(): void
    {
        $tagService = $this->createMock(TagServiceInterface::class);
        $transformer = new TagsDataTransformer($tagService);

        $t1 = new Tag();
        $t1->setTitle('Nature');
        $t2 = new Tag();
        $t2->setTitle('Portrait');

        $out = $transformer->transform(new ArrayCollection([$t1, $t2]));
        $this->assertSame('Nature, Portrait', $out);
    }

    /**
     * Test that reverseTransform finds existing tags and creates new ones if needed.
     */
    public function testReverseTransformFindsExistingAndCreatesNew(): void
    {
        $tagService = $this->createMock(TagServiceInterface::class);

        $existingNature = new Tag();
        $existingNature->setTitle('Nature');
        $existingMacro = new Tag();
        $existingMacro->setTitle('Macro');

        $tagService->expects($this->exactly(3))
            ->method('findOneByTitle')
            ->withConsecutive(
                [$this->equalTo('nature')],
                [$this->equalTo('portrait')],
                [$this->equalTo('macro')]
            )
            ->willReturnOnConsecutiveCalls(
                $existingNature,
                null,
                $existingMacro
            );

        $tagService->expects($this->once())
            ->method('save')
            ->with($this->callback(function ($tag) {
                return $tag instanceof Tag && 'portrait' === $tag->getTitle();
            }));

        $transformer = new TagsDataTransformer($tagService);

        $result = $transformer->reverseTransform('Nature, portrait, Macro');

        $this->assertCount(3, $result);
        $this->assertSame($existingNature, $result[0]);
        $this->assertInstanceOf(Tag::class, $result[1]);
        $this->assertSame('portrait', $result[1]->getTitle());
        $this->assertSame($existingMacro, $result[2]);
    }

    /**
     * Test that reverseTransform skips empty tokens.
     */
    public function testReverseTransformSkipsEmptyTokens(): void
    {
        $tagService = $this->createMock(TagServiceInterface::class);
        $tagService->expects($this->never())->method('save');
        $tagService->expects($this->never())->method('findOneByTitle');

        $transformer = new TagsDataTransformer($tagService);

        $result = $transformer->reverseTransform(' , ,  ,');
        $this->assertSame([], $result);
    }
}
