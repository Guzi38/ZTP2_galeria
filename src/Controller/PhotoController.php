<?php

/**
 * Photo controller.
 */

namespace App\Controller;

use App\Entity\Photo;
use App\Form\Type\PhotoEditType;
use App\Form\Type\PhotoType;
use App\Service\PhotoServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class PhotoController.
 */
#[Route('/photo')]
class PhotoController extends AbstractController
{
    public function __construct(
        private readonly PhotoServiceInterface $photoService,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[Route(name: 'photo_index', methods: 'GET')]
    public function index(Request $request): Response
    {
        $filters = $this->getFilters($request);
        $pagination = $this->photoService->getPaginatedList(
            $request->query->getInt('page', 1),
            $filters
        );

        return $this->render('photo/index.html.twig', ['pagination' => $pagination]);
    }

    #[Route('/{id}', name: 'photo_show', requirements: ['id' => '[1-9]\d*'], methods: 'GET')]
    public function show(Photo $photo): Response
    {
        return $this->render('photo/show.html.twig', ['photo' => $photo]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/create', name: 'photo_create', methods: 'GET|POST')]
    public function create(Request $request): Response
    {
        $photo = new Photo();
        $form = $this->createForm(PhotoType::class, $photo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile|null $file */
            $file = $form->get('file')->getData();
            $user = $this->getUser();

            $photo->setAuthor($user);
            $photo->setCreatedAt(new \DateTimeImmutable());

            $this->photoService->create($file, $photo, $user);

            $this->addFlash('success', $this->translator->trans('message.created_successfully'));

            return $this->redirectToRoute('photo_index');
        }

        return $this->render('photo/create.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/{id}/edit', name: 'photo_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    public function edit(Request $request, Photo $photo): Response
    {
        $this->denyAccessUnlessGranted('EDIT', $photo);

        $user = $this->getUser();
        $form = $this->createForm(
            PhotoEditType::class,
            $photo,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('photo_edit', ['id' => $photo->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile|null $file */
            $file = $form->get('file')->getData();
            $this->photoService->update($file, $photo, $user);

            $this->addFlash('success', $this->translator->trans('message.edited_successfully'));

            return $this->redirectToRoute('photo_index');
        }

        return $this->render('photo/edit.html.twig', [
            'form' => $form->createView(),
            'photo' => $photo,
        ]);
    }

    #[Route('/{id}/delete', name: 'photo_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    public function delete(Request $request, Photo $photo): Response
    {
        $this->denyAccessUnlessGranted('DELETE', $photo);

        $form = $this->createForm(
            FormType::class,
            $photo,
            [
                'method' => 'DELETE',
                'action' => $this->generateUrl('photo_delete', ['id' => $photo->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->photoService->delete($photo);
            $this->addFlash('success', $this->translator->trans('message.deleted_successfully'));

            return $this->redirectToRoute('photo_index');
        }

        return $this->render('photo/delete.html.twig', [
            'form' => $form->createView(),
            'photo' => $photo,
        ]);
    }

    private function getFilters(Request $request): array
    {
        return [
            'gallery_id' => $request->query->getInt('filters_gallery_id'),
            'photos_tags_id' => $request->query->getInt('filters_tags_id'),
        ];
    }
}
