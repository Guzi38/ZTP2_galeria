<?php

/**
 * Registration controller.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RegistrationController.
 */
class RegistrationController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param EntityManagerInterface      $em             Entity manager
     * @param UserPasswordHasherInterface $passwordHasher Password hasher
     */
    public function __construct(private readonly EntityManagerInterface $em, private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    /**
     * Register action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(Request $request): Response
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plain = (string) $user->getPassword();
            $user->setPassword(
                $this->passwordHasher->hashPassword($user, $plain)
            );

            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('success', 'Konto utworzone. Możesz się zalogować.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
