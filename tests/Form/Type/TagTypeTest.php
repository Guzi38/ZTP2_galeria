<?php

namespace App\Tests\Form\Type;

use App\Entity\Tag;
use App\Form\Type\TagType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;

class TagTypeTest extends KernelTestCase
{
    private FormFactoryInterface $formFactory;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->formFactory = static::getContainer()->get(FormFactoryInterface::class);
    }

    public function testBuildFormFields(): void
    {
        $form = $this->formFactory->create(TagType::class);

        $this->assertTrue($form->has('title'));
    }

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

    public function testConfigureOptions(): void
    {
        $form = $this->formFactory->create(TagType::class);
        $this->assertSame(Tag::class, $form->getConfig()->getOption('data_class'));
    }

    public function testGetBlockPrefix(): void
    {
        $type = new TagType();
        $this->assertSame('tag', $type->getBlockPrefix());
    }
}
