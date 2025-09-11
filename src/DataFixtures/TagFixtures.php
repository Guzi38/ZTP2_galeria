<?php

/**
 * Tag fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Tag;

/**
 * Class TagFixtures.
 */
class TagFixtures extends AbstractBaseFixtures
{
    /**
     * Load data.
     */
    protected function loadData(): void
    {
        if (null === $this->manager || null === $this->faker) {
            return;
        }

        $this->createMany(11, 'tags', function (int $i) {
            $tag = new Tag();
            $tag->setTitle($this->faker->unique()->word);
            $tag->setSlug($this->faker->unique()->slug);
            $tag->setCreatedAt(\DateTimeImmutable::createFromMutable(
                $this->faker->dateTimeBetween('-100 days', '-1 days')
            ));
            $tag->setUpdatedAt(\DateTimeImmutable::createFromMutable(
                $this->faker->dateTimeBetween('-100 days', '-1 days')
            ));

            return $tag;
        });

        $this->manager->flush();
    }
}
