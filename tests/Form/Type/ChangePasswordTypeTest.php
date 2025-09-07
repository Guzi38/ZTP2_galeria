<?php

namespace App\Tests\Form\Type;

use App\Form\Type\ChangePasswordType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;

class ChangePasswordTypeTest extends KernelTestCase
{
    private FormFactoryInterface $formFactory;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->formFactory = static::getContainer()->get(FormFactoryInterface::class);
    }

    public function testFormHasPlainPasswordField(): void
    {
        $form = $this->formFactory->create(ChangePasswordType::class);

        $this->assertTrue($form->has('plainPassword'));
    }

    public function testSubmitValidPassword(): void
    {
        $form = $this->formFactory->create(ChangePasswordType::class);

        $form->submit([
            'plainPassword' => [
                'first' => 'secret123',
                'second' => 'secret123',
            ],
        ]);

        $this->assertTrue($form->isSynchronized());
        $this->assertTrue($form->has('plainPassword'));
    }

    public function testSubmitInvalidPasswordMismatch(): void
    {
        $form = $this->formFactory->create(ChangePasswordType::class);

        $form->submit([
            'plainPassword' => [
                'first' => 'secret123',
                'second' => 'notthesame',
            ],
        ]);

        $this->assertFalse($form->isValid());
    }
}
