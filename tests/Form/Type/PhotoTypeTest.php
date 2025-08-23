<?php

namespace App\Tests\Form\Type;

use App\Entity\Photo;
use App\Form\Type\PhotoType;
use App\Form\DataTransformer\TagsDataTransformer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;

class PhotoTypeTest extends KernelTestCase
{
    private FormFactoryInterface $formFactory;

    protected function setUp(): void
    {
        self::bootKernel();

        $mockTransformer = $this->createMock(TagsDataTransformer::class);
        $mockTransformer->method('transform')->willReturn('');
        $mockTransformer->method('reverseTransform')->willReturn([]);

        static::getContainer()->set(TagsDataTransformer::class, $mockTransformer);

        $this->formFactory = static::getContainer()->get(FormFactoryInterface::class);
    }

    public function testBuildFormFields(): void
    {
        $form = $this->formFactory->create(PhotoType::class);

        foreach (['title', 'content', 'gallery', 'tags', 'file'] as $field) {
            $this->assertTrue($form->has($field), sprintf('Form should have field "%s"', $field));
        }
    }

    public function testSubmitValidData(): void
    {
        $formData = [
            'title' => 'Test title',
            'content' => 'Test content',
            'tags' => 'tag1,tag2',
        ];

        $model = new Photo();
        $form = $this->formFactory->create(PhotoType::class, $model);

        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertSame('Test title', $model->getTitle());
        $this->assertSame('Test content', $model->getContent());
    }

    public function testConfigureOptions(): void
    {
        $form = $this->formFactory->create(PhotoType::class);
        $this->assertSame(Photo::class, $form->getConfig()->getOption('data_class'));
    }

    public function testGetBlockPrefix(): void
    {
        $type = static::getContainer()->get(PhotoType::class);
        $this->assertSame('photo', $type->getBlockPrefix());
    }
}
