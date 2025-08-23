<?php

namespace App\Tests\Form\DataTransformer;

use App\Entity\Tag;
use App\Form\DataTransformer\TagsDataTransformer;
use App\Service\TagServiceInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class TagsDataTransformerTest extends TestCase
{
    public function testTransformEmptyCollectionReturnsEmptyString(): void
    {
        $tagService = $this->createMock(TagServiceInterface::class);
        $transformer = new TagsDataTransformer($tagService);

        $out = $transformer->transform(new ArrayCollection());
        $this->assertSame('', $out);
    }

    public function testTransformReturnsCommaSeparatedTitles(): void
    {
        $tagService = $this->createMock(TagServiceInterface::class);
        $transformer = new TagsDataTransformer($tagService);

        $t1 = new Tag(); $t1->setTitle('Nature');
        $t2 = new Tag(); $t2->setTitle('Portrait');

        $out = $transformer->transform(new ArrayCollection([$t1, $t2]));
        $this->assertSame('Nature, Portrait', $out);
    }

    public function testReverseTransformFindsExistingAndCreatesNew(): void
    {
        $tagService = $this->createMock(TagServiceInterface::class);

        $existingNature = new Tag();  $existingNature->setTitle('Nature');
        $existingMacro  = new Tag();  $existingMacro->setTitle('Macro');

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
                return $tag instanceof Tag && $tag->getTitle() === 'portrait';
            }));

        $transformer = new TagsDataTransformer($tagService);

        $result = $transformer->reverseTransform('Nature, portrait, Macro');

        $this->assertCount(3, $result);
        $this->assertSame($existingNature, $result[0]);
        $this->assertInstanceOf(Tag::class, $result[1]);
        $this->assertSame('portrait', $result[1]->getTitle());
        $this->assertSame($existingMacro, $result[2]);
    }

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
