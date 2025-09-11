<?php

/**
 * Tests for PhotoService.
 */

namespace App\Tests\Service;

use App\Entity\Photo;
use App\Entity\User;
use App\Repository\PhotoRepository;
use App\Service\FileUploadServiceInterface;
use App\Service\GalleryServiceInterface;
use App\Service\PhotoService;
use App\Service\TagServiceInterface;
use Knp\Component\Pager\PaginatorInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class PhotoServiceTest.
 */
class PhotoServiceTest extends TestCase
{
    private PhotoRepository $photoRepository;
    private PaginatorInterface $paginator;
    private TagServiceInterface $tagService;
    private GalleryServiceInterface $galleryService;
    private FileUploadServiceInterface $fileUploadService;
    private Filesystem $filesystem;
    private PhotoService $service;

    /**
     * Set up mocks and the service under test.
     */
    protected function setUp(): void
    {
        $this->photoRepository = $this->createMock(PhotoRepository::class);
        $this->paginator = $this->createMock(PaginatorInterface::class);
        $this->tagService = $this->createMock(TagServiceInterface::class);
        $this->galleryService = $this->createMock(GalleryServiceInterface::class);
        $this->fileUploadService = $this->createMock(FileUploadServiceInterface::class);
        $this->filesystem = $this->createMock(Filesystem::class);

        $this->service = new PhotoService(
            '/tmp',
            $this->photoRepository,
            $this->paginator,
            $this->tagService,
            $this->galleryService,
            $this->fileUploadService,
            $this->filesystem
        );
    }

    /**
     * Test that save() delegates to repository.
     */
    public function testSaveCallsRepository(): void
    {
        $photo = new Photo();
        $this->photoRepository->expects($this->once())->method('save')->with($photo);

        $this->service->save($photo);
    }

    /**
     * Test that delete() delegates to repository.
     */
    public function testDeleteCallsRepository(): void
    {
        $photo = new Photo();
        $this->photoRepository->expects($this->once())->method('delete')->with($photo);

        $this->service->delete($photo);
    }

    /**
     * Test that prepareFilters() maps gallery_id and tag_id.
     */
    public function testPrepareFiltersWithGalleryAndTag(): void
    {
        $gallery = $this->createMock(\App\Entity\Gallery::class);
        $tag = $this->createMock(\App\Entity\Tag::class);

        $this->galleryService->method('findOneById')->willReturn($gallery);
        $this->tagService->method('findOneById')->willReturn($tag);

        $filters = $this->service->prepareFilters(['gallery_id' => 1, 'tag_id' => 2]);

        $this->assertArrayHasKey('gallery', $filters);
        $this->assertArrayHasKey('tag', $filters);
    }

    /**
     * Test that create() uploads file and saves photo.
     */
    public function testCreateUploadsFileAndSavesPhoto(): void
    {
        $file = $this->createMock(UploadedFile::class);
        $this->fileUploadService->method('upload')->willReturn('file.jpg');

        $photo = new Photo();
        $user = new User();

        $this->photoRepository->expects($this->once())->method('save')->with($photo);

        $this->service->create($file, $photo, $user);

        $this->assertSame('file.jpg', $photo->getFilename());
        $this->assertSame($user, $photo->getAuthor());
    }

    /**
     * Test that update() replaces old file with new one.
     */
    public function testUpdateReplacesFile(): void
    {
        $file = $this->createMock(UploadedFile::class);
        $this->fileUploadService->method('upload')->willReturn('new.jpg');

        $photo = new Photo();
        $photo->setFilename('old.jpg');
        $user = new User();

        $this->filesystem->expects($this->once())->method('remove')->with('/tmp/old.jpg');
        $this->photoRepository->expects($this->once())->method('save')->with($photo);

        $this->service->update($file, $photo, $user);

        $this->assertSame('new.jpg', $photo->getFilename());
        $this->assertSame($user, $photo->getAuthor());
    }

    /**
     * Test that update() without file keeps filename unchanged.
     */
    public function testUpdateWithoutFileKeepsFilename(): void
    {
        $photo = new Photo();
        $photo->setFilename('keep.jpg');
        $user = new User();

        $this->photoRepository->expects($this->once())->method('save')->with($photo);

        $this->service->update(null, $photo, $user);

        $this->assertSame('keep.jpg', $photo->getFilename());
    }
}
