<?php

/**
 * Tests for ChangePasswordType form.
 */

namespace App\Tests\Form\Type;

use App\Form\Type\ChangePasswordType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Class ChangePasswordTypeTest.
 */
class ChangePasswordTypeTest extends KernelTestCase
{
    private FormFactoryInterface $formFactory;

    /**
     * Boot the kernel and get the form factory.
     */
    protected function setUp(): void
    {
        self::bootKernel();
        $this->formFactory = static::getContainer()->get(FormFactoryInterface::class);
    }

    /**
     * Test that the form has a plainPassword field.
     */
    public function testFormHasPlainPasswordField(): void
    {
        $form = $this->formFactory->create(ChangePasswordType::class);

        $this->assertTrue($form->has('plainPassword'));
    }

    /**
     * Test that valid password submission is synchronized.
     */
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

    /**
     * Test that invalid (mismatched) passwords make the form invalid.
     */
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
