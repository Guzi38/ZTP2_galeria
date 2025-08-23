<?php

namespace App\Tests\Form\Type;

use App\Form\Type\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;

class RegistrationTypeTest extends KernelTestCase
{
    private FormFactoryInterface $formFactory;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->formFactory = static::getContainer()->get(FormFactoryInterface::class);
    }

    public function testBuildFormFields(): void
    {
        $form = $this->formFactory->create(RegistrationType::class);

        $this->assertTrue($form->has('email'));
        $this->assertTrue($form->has('password'));
    }

    public function testSubmitValidData(): void
    {
        $formData = [
            'email' => 'test@example.com',
            'password' => [
                'first' => 'secret123',
                'second' => 'secret123',
            ],
        ];

        $form = $this->formFactory->create(RegistrationType::class);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertSame('test@example.com', $form->get('email')->getData());
        $this->assertSame('secret123', $form->get('password')->getData());
    }

    public function testGetBlockPrefix(): void
    {
        $type = new RegistrationType();
        $this->assertSame('user', $type->getBlockPrefix());
    }
}
