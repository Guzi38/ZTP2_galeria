<?php

namespace App\DataFixtures;

use App\Entity\Gallery;
use App\Entity\Photo;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class PhotoFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    protected function loadData(): void
    {
        if (null === $this->manager || null === $this->faker) {
            return;
        }

        $this->createMany(10, 'photos', function (int $i) {
            $photo = new Photo();
            $photo->setTitle($this->faker->sentence);
            $photo->setContent($this->faker->text);
            $photo->setCreatedAt(\DateTimeImmutable::createFromMutable(
                $this->faker->dateTimeBetween('-100 days', '-1 days')
            ));
            $photo->setUpdatedAt(\DateTimeImmutable::createFromMutable(
                $this->faker->dateTimeBetween('-100 days', '-1 days')
            ));

            /** @var Gallery $gallery */
            $gallery = $this->getRandomReference('galleries');
            $photo->setGallery($gallery);

            /** @var Tag[] $tags */
            $tags = $this->getRandomReferences('tags', $this->faker->numberBetween(0, 5));
            foreach ($tags as $tag) {
                $photo->addTag($tag);
            }

            /** @var User $author */
            $author = $this->getRandomReference('users');
            $photo->setAuthor($author);

            return $photo;
        });

        $this->manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            GalleryFixtures::class,
            TagFixtures::class,
            UserFixtures::class,
        ];
    }
}
