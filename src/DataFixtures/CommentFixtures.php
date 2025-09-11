<?php

/**
 * Comment fixtures.
 */

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Photo;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class CommentFixtures.
 */
class CommentFixtures extends AbstractBaseFixtures implements DependentFixtureInterface
{
    /**
     * Get dependencies.
     *
     * @return array<int, string> Array of dependent fixture classes
     */
    public function getDependencies(): array
    {
        return [
            PhotoFixtures::class,
            UserFixtures::class,
        ];
    }

    /**
     * Load data.
     */
    protected function loadData(): void
    {
        if (null === $this->manager || null === $this->faker) {
            return;
        }

        $this->createMany(10, 'comments', function (int $i) {
            $comment = new Comment();
            $comment->setContent($this->faker->sentence);

            /** @var Photo $photo */
            $photo = $this->getRandomReference('photos');
            $comment->setPhoto($photo);

            /** @var User $author */
            $author = $this->getRandomReference('users');
            $comment->setAuthor($author);

            return $comment;
        });

        $this->manager->flush();
    }
}
