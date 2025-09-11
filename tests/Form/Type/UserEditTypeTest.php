<?php

/**
 * Tests for UserEditType form.
 */

namespace App\Tests\Form\Type;

use App\Entity\User;
use App\Form\Type\UserEditType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * Class UserEditTypeTest.
 */
class UserEditTypeTest extends KernelTestCase
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
     * Test that the form has the email field with correct options.
     */
    public function testBuildFormHasEmailField(): void
    {
        $form = $this->formFactory->create(UserEditType::class);

        $this->assertTrue($form->has('email'));
        $cfg = $form->get('email')->getConfig();

        $this->assertTrue($cfg->getOption('required'));
        $this->assertSame('', $cfg->getOption('empty_data'));
        $this->assertSame('Email', $cfg->getOption('label'));
    }

    /**
     * Test that submitting valid data updates the entity.
     */
    public function testSubmitValidData(): void
    {
        $user = new User();
        $form = $this->formFactory->create(UserEditType::class, $user);

        $formData = ['email' => 'test@example.com'];
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertSame('test@example.com', $user->getEmail());
    }

    /**
     * Test that the block prefix is 'user_edit'.
     */
    public function testBlockPrefix(): void
    {
        $type = new UserEditType();
        $this->assertSame('user_edit', $type->getBlockPrefix());
    }
}
