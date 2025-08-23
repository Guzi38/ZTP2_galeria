<?php

namespace App\Tests\Form\Type;

use App\Entity\Photo;
use App\Form\DataTransformer\TagsDataTransformer;
use App\Form\Type\PhotoEditType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PhotoEditTypeTest extends TestCase
{
    /** @var TagsDataTransformer|MockObject */
    private $tagsDataTransformer;

    protected function setUp(): void
    {
        $this->tagsDataTransformer = $this->createMock(TagsDataTransformer::class);
    }

    public function testBuildFormAddsFieldsAndTransformer(): void
    {
        $type = new PhotoEditType($this->tagsDataTransformer);

        /** @var FormBuilderInterface|MockObject $builder */
        $builder = $this->getMockBuilder(FormBuilderInterface::class)->getMock();
        $builder->method('add')->willReturnSelf();

        /** @var FormBuilderInterface|MockObject $tagsChildBuilder */
        $tagsChildBuilder = $this->getMockBuilder(FormBuilderInterface::class)->getMock();
        $tagsChildBuilder
            ->expects($this->once())
            ->method('addModelTransformer')
            ->with($this->tagsDataTransformer)
            ->willReturnSelf();

        $builder
            ->expects($this->once())
            ->method('get')
            ->with('tags')
            ->willReturn($tagsChildBuilder);

        $builder->expects($this->atLeast(1))
            ->method('add')
            ->withConsecutive(
                ['title', TextType::class, $this->callback(fn($o) => isset($o['label']) && isset($o['required']))],
                ['content', TextType::class, $this->callback(fn($o) => isset($o['label']) && isset($o['required']))],
                ['gallery', EntityType::class, $this->callback(fn($o) => isset($o['class']) && isset($o['choice_label']))],
                ['tags', TextType::class, $this->callback(fn($o) => array_key_exists('required', $o))],
                ['file', FileType::class, $this->callback(fn($o) => array_key_exists('mapped', $o))]
            );

        $type->buildForm($builder, []);
        $this->addToAssertionCount(1);
    }

    public function testConfigureOptionsSetsDataClass(): void
    {
        $type = new PhotoEditType($this->tagsDataTransformer);
        $resolver = new OptionsResolver();
        $type->configureOptions($resolver);

        $resolved = $resolver->resolve([]);
        $this->assertArrayHasKey('data_class', $resolved);
        $this->assertSame(Photo::class, $resolved['data_class']);
    }

    public function testBlockPrefix(): void
    {
        $type = new PhotoEditType($this->tagsDataTransformer);
        $this->assertSame('photo', $type->getBlockPrefix());
    }
}
