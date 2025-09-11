<?php

/**
 * Tests for CommentType form.
 */

namespace App\Tests\Form\Type;

use App\Entity\Comment;
use App\Form\Type\CommentType;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Test\TypeTestCase;

#[CoversClass(CommentType::class)]
/**
 * Class CommentTypeTest.
 */
class CommentTypeTest extends TypeTestCase
{
    /**
     * Test that the form has expected fields and options.
     */
    public function testFormHasExpectedFieldsAndOptions(): void
    {
        $form = $this->factory->create(CommentType::class);

        $this->assertTrue($form->has('content'));

        $contentConfig = $form->get('content')->getConfig();
        $this->assertSame(TextType::class, get_class($contentConfig->getType()->getInnerType()));

        $this->assertSame('Content', $contentConfig->getOption('label'));
        $this->assertTrue($contentConfig->getOption('required'));

        $attr = $contentConfig->getOption('attr');
        $this->assertIsArray($attr);
        $this->assertArrayHasKey('max_length', $attr);
        $this->assertSame(6000, $attr['max_length']);

        $this->assertSame(Comment::class, $form->getConfig()->getOption('data_class'));
    }

    /**
     * Test that valid data submission maps correctly to the entity.
     */
    public function testSubmitValidDataMapsToEntity(): void
    {
        $comment = new Comment();

        $form = $this->factory->create(CommentType::class, $comment);
        $formData = [
            'content' => 'Hello world',
        ];

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertSame('Hello world', $comment->getContent());
    }

    /**
     * Test that the block prefix is "comment".
     */
    public function testBlockPrefix(): void
    {
        $type = new CommentType();
        $this->assertSame('comment', $type->getBlockPrefix());
    }
}
