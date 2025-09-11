<?php

/**
 * Tests for UserType form.
 */

namespace App\Tests\Form\Type;

use App\Entity\User;
use App\Form\Type\UserType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Class UserTypeTest.
 */
class UserTypeTest extends KernelTestCase
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
     * Test that the form has required fields.
     */
    public function testBuildFormFields(): void
    {
        $form = $this->formFactory->create(UserType::class);

        $this->assertTrue($form->has('email'), 'Form should have "email" field');
        $this->assertTrue($form->has('password'), 'Form should have "password" field');
    }

    /**
     * Test that submitting valid data maps to the entity.
     */
    public function testSubmitValidData(): void
    {
        $formData = [
            'email' => 'test@example.com',
            'password' => 'secret',
        ];

        $model = new User();
        $form = $this->formFactory->create(UserType::class, $model);
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertSame('test@example.com', $model->getEmail());
        $this->assertSame('secret', $model->getPassword());
    }

    /**
     * Test that the form configures data_class correctly.
     */
    public function testConfigureOptions(): void
    {
        $form = $this->formFactory->create(UserType::class);
        $this->assertSame(User::class, $form->getConfig()->getOption('data_class'));
    }

    /**
     * Test that block prefix is 'user'.
     */
    public function testGetBlockPrefix(): void
    {
        $type = new UserType();
        $this->assertSame('user', $type->getBlockPrefix());
    }
}
