<?php

/**
 * Gallery fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Gallery;

/**
 * Class GalleryFixtures.
 */
class GalleryFixtures extends AbstractBaseFixtures
{
    /**
     * Load data.
     */
    protected function loadData(): void
    {
        if (null === $this->manager || null === $this->faker) {
            return;
        }

        $this->createMany(10, 'galleries', function (int $i) {
            $gallery = new Gallery();
            $gallery->setTitle($this->faker->unique()->word);
            $gallery->setSlug($this->faker->unique()->slug);
            $gallery->setCreatedAt(\DateTimeImmutable::createFromMutable(
                $this->faker->dateTimeBetween('-100 days', '-1 days')
            ));
            $gallery->setUpdatedAt(\DateTimeImmutable::createFromMutable(
                $this->faker->dateTimeBetween('-100 days', '-1 days')
            ));

            return $gallery;
        });

        $this->manager->flush();
    }
}
