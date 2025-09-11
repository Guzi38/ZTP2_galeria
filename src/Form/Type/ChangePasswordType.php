<?php

/**
 * ChangePasswordType form.
 */

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class ChangePasswordType.
 *
 * Form type for changing user password.
 */
class ChangePasswordType extends AbstractType
{
    /**
     * Builds the change password form.
     *
     * @param FormBuilderInterface $builder Form builder
     * @param array<string, mixed> $options Options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('plainPassword', RepeatedType::class, [
            'type' => PasswordType::class,
            'mapped' => false, // sami zhashujemy i zapiszemy w serwisie
            'first_options' => [
                'label' => 'Hasło',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(min: 6, max: 4096),
                ],
            ],
            'second_options' => [
                'label' => 'Powtórz hasło',
            ],
            'invalid_message' => 'Hasła muszą być identyczne.',
        ]);
    }
}
