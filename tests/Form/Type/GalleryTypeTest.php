<?php

/**
 * Tests for GalleryType form.
 */

namespace App\Tests\Form\Type;

use App\Entity\Gallery;
use App\Form\Type\GalleryType;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Test\TypeTestCase;

#[CoversClass(GalleryType::class)]
/**
 * Class GalleryTypeTest.
 */
class GalleryTypeTest extends TypeTestCase
{
    /**
     * Test that the form has expected fields and options.
     */
    public function testFormHasExpectedFieldsAndOptions(): void
    {
        $form = $this->factory->create(GalleryType::class);

        $this->assertTrue($form->has('title'));

        $cfg = $form->get('title')->getConfig();
        $this->assertSame(TextType::class, get_class($cfg->getType()->getInnerType()));

        $this->assertSame('Title', $cfg->getOption('label'));
        $this->assertTrue($cfg->getOption('required'));

        $attr = $cfg->getOption('attr');
        $this->assertIsArray($attr);
        $this->assertArrayHasKey('max_length', $attr);
        $this->assertSame(64, $attr['max_length']);

        $this->assertSame(Gallery::class, $form->getConfig()->getOption('data_class'));
    }

    /**
     * Test that valid data submission maps to the entity.
     */
    public function testSubmitValidDataMapsToEntity(): void
    {
        $gallery = new Gallery();

        $form = $this->factory->create(GalleryType::class, $gallery);
        $formData = ['title' => 'Summer Vibes'];

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertSame('Summer Vibes', $gallery->getTitle());
    }

    /**
     * Test that the block prefix is "gallery".
     */
    public function testBlockPrefix(): void
    {
        $type = new GalleryType();
        $this->assertSame('gallery', $type->getBlockPrefix());
    }
}
