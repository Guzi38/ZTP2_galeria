<?php

/**
 * User controller.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\ChangePasswordType;
use App\Form\Type\UserEditType;
use App\Service\UserServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class UserController.
 */
#[Route('/user')]
class UserController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param UserServiceInterface $userService User service
     * @param TranslatorInterface  $translator  Translator
     */
    public function __construct(private readonly UserServiceInterface $userService, private readonly TranslatorInterface $translator)
    {
    }

    /**
     * List users.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route(name: 'user_index', methods: ['GET'])]
    #[IsGranted('MANAGE')]
    public function index(Request $request): Response
    {
        $pagination = $this->userService->createPaginatedList(
            $request->query->getInt('page', 1)
        );

        return $this->render('user/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Show user.
     *
     * @param User $user User entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}', name: 'user_show', requirements: ['id' => '[1-9]\d*'], methods: ['GET'])]
    #[IsGranted('MANAGE')]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', ['user' => $user]);
    }

    /**
     * Edit profile data (no password here).
     *
     * @param Request $request HTTP request
     * @param User    $user    User entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/edit', name: 'user_edit', requirements: ['id' => '[1-9]\d*'], methods: ['GET', 'PUT'])]
    #[IsGranted('EDIT', subject: 'user')]
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(
            UserEditType::class,
            $user,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('user_edit', ['id' => $user->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->userService->updateProfile($user);

            $this->addFlash('success', $this->translator->trans('message.updated_successfully'));

            return $this->redirectToRoute('user_edit', ['id' => $user->getId()]);
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    /**
     * Change password (separate form and route).
     *
     * @param Request $request HTTP request
     * @param User    $user    User entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/password', name: 'user_change_password', requirements: ['id' => '[1-9]\d*'], methods: ['GET', 'POST'])]
    #[IsGranted('EDIT', subject: 'user')]
    public function changePassword(Request $request, User $user): Response
    {
        $form = $this->createForm(ChangePasswordType::class, null, [
            'action' => $this->generateUrl('user_change_password', ['id' => $user->getId()]),
            'method' => 'POST',
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plain */
            $plain = (string) $form->get('plainPassword')->getData();

            $this->userService->changePassword($user, $plain);

            $this->addFlash('success', $this->translator->trans('message.password_changed'));

            return $this->redirectToRoute('user_change_password', ['id' => $user->getId()]);
        }

        return $this->render('user/change_password.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
}
