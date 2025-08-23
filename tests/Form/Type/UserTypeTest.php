<?php

namespace App\Tests\Form\Type;

use App\Entity\User;
use App\Form\Type\UserType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Form\FormFactoryInterface;

class UserTypeTest extends KernelTestCase
{
    private FormFactoryInterface $formFactory;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->formFactory = static::getContainer()->get(FormFactoryInterface::class);
    }

    public function testBuildFormFields(): void
    {
        $form = $this->formFactory->create(UserType::class);

        $this->assertTrue($form->has('email'), 'Form should have "email" field');
        $this->assertTrue($form->has('password'), 'Form should have "password" field');
    }

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

    public function testConfigureOptions(): void
    {
        $form = $this->formFactory->create(UserType::class);
        $this->assertSame(User::class, $form->getConfig()->getOption('data_class'));
    }

    public function testGetBlockPrefix(): void
    {
        $type = new UserType();
        $this->assertSame('user', $type->getBlockPrefix());
    }
}
