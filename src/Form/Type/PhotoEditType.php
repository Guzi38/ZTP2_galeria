<?php

/**
 * Edit photo form.
 */

namespace App\Form\Type;

use App\Entity\Photo;
use App\Form\DataTransformer\TagsDataTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

/**
 * Class PhotoEditType.
 *
 * Form type for editing a photo entity.
 */
class PhotoEditType extends AbstractType
{
    /**
     * Constructor.
     *
     * @param TagsDataTransformer $tagsDataTransformer Data transformer for tags
     */
    public function __construct(private readonly TagsDataTransformer $tagsDataTransformer)
    {
    }

    /**
     * Builds the photo edit form.
     *
     * @param FormBuilderInterface $builder Form builder
     * @param array<string, mixed> $options Options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'label_title',
                'required' => false,
                'empty_data' => '',
                'attr' => ['max_length' => 120],
            ])
            ->add('content', TextType::class, [
                'label' => 'label.content',
                'required' => false,
                'empty_data' => '',
                'attr' => ['max_length' => 65000],
            ])
            ->add('gallery', EntityType::class, [
                'label' => 'label.gallery',
                'class' => 'App\Entity\Gallery',
                'placeholder' => 'label.gallery',
                'choice_label' => 'title',
            ])
            ->add('tags', TextType::class, [
                'label' => 'label_tags',
                'required' => false,
                'empty_data' => '',
                'attr' => ['max_length' => 128],
            ])
            ->add('file', FileType::class, [
                'mapped' => false,
                'label' => 'label.photo',
                'required' => false, // w edycji plik nie jest wymagany
                'constraints' => new Image([
                    'maxSize' => '1024k',
                    'mimeTypes' => ['image/png', 'image/jpeg', 'image/pjpeg'],
                ]),
            ])
        ;

        $builder->get('tags')->addModelTransformer($this->tagsDataTransformer);
    }

    /**
     * Configures options for this form.
     *
     * @param OptionsResolver $resolver Options resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Photo::class]);
    }

    /**
     * Returns block prefix for this form.
     *
     * @return string Block prefix
     */
    public function getBlockPrefix(): string
    {
        return 'photo';
    }
}
