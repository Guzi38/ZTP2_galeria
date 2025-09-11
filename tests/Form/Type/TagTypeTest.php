<?php

/**
 * Tests for TagType form.
 */

namespace App\Tests\Form\Type;

use App\Entity\Tag;
use App\Form\Type\TagType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Class TagTypeTest.
 */
class TagTypeTest extends KernelTestCase
{
    private FormFactoryInterface $formFactory;

    /**
     * Set up form factory.
     */
    protected function setUp(): void
    {
        self::bootKernel();
        $this->formFactory = static::getContainer()->get(FormFactoryInterface::class);
    }

    /**
     * Test that the form has the expected fields.
     */
    public function testBuildFormFields(): void
    {
        $form = $this->formFactory->create(TagType::class);

        $this->assertTrue($form->has('title'));
    }

    /**
     * Test that submitting valid data maps correctly to the entity.
     */
    public function testSubmitValidData(): void
    {
        $formData = [
            'title' => 'Nature',
        ];

        $model = new Tag();
        $form = $this->formFactory->create(TagType::class, $model);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertSame('Nature', $model->getTitle());
    }

    /**
     * Test that the form sets the correct data_class option.
     */
    public function testConfigureOptions(): void
    {
        $form = $this->formFactory->create(TagType::class);
        $this->assertSame(Tag::class, $form->getConfig()->getOption('data_class'));
    }

    /**
     * Test that the block prefix is 'tag'.
     */
    public function testGetBlockPrefix(): void
    {
        $type = new TagType();
        $this->assertSame('tag', $type->getBlockPrefix());
    }
}
