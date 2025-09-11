<?php

/**
 * Abstract base fixtures.
 */

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

/**
 * Class AbstractBaseFixtures.
 */
abstract class AbstractBaseFixtures extends Fixture
{
    /**
     * Faker generator.
     */
    protected ?Generator $faker = null;

    /**
     * Object manager.
     */
    protected ?ObjectManager $manager = null;

    /**
     * References index.
     *
     * @var array<string, array<int, string>>
     */
    private array $referencesIndex = [];

    /**
     * Group class map.
     *
     * @var array<string, class-string>
     */
    private static array $groupClassMap = [];

    /**
     * Load fixtures.
     *
     * @param ObjectManager $manager Object manager
     */
    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $this->faker = Factory::create();
        $this->loadData();
    }

    /**
     * Get a random reference for a group.
     *
     * @param string $groupName Group name
     *
     * @return object Random entity
     */
    public function getRandomReference(string $groupName): object
    {
        if (empty($this->referencesIndex[$groupName] ?? [])) {
            $this->buildReferencesIndex($groupName);
        }

        if (empty($this->referencesIndex[$groupName])) {
            throw new \InvalidArgumentException(sprintf('No references found for group "%s"', $groupName));
        }

        $class = $this->getGroupClass($groupName);
        $names = $this->referencesIndex[$groupName];
        $randomName = $names[array_rand($names)];

        return $this->getReference($randomName, $class);
    }

    /**
     * Get multiple random references.
     *
     * @param string $groupName Group name
     * @param int    $count     Number of references
     *
     * @return object[] List of entities
     */
    public function getRandomReferences(string $groupName, int $count): array
    {
        $result = [];
        while (count($result) < $count) {
            $result[] = $this->getRandomReference($groupName);
        }

        return $result;
    }

    /**
     * Load data (to be implemented in child classes).
     */
    abstract protected function loadData(): void;

    /**
     * Create many entities.
     *
     * @param int      $count     Number of entities
     * @param string   $groupName Group name
     * @param callable $factory   Factory function
     */
    protected function createMany(int $count, string $groupName, callable $factory): void
    {
        for ($i = 0; $i < $count; ++$i) {
            $entity = $factory($i);
            if (null === $entity) {
                throw new \LogicException('Factory must return an entity object.');
            }

            $this->manager->persist($entity);

            $refName = sprintf('%s_%d', $groupName, $i);
            $this->addReference($refName, $entity);

            self::$groupClassMap[$groupName] = \get_class($entity);

            $this->referencesIndex[$groupName][] = $refName;
        }
    }

    /**
     * Get group class.
     *
     * @param string $groupName Group name
     *
     * @return class-string Entity class
     */
    private function getGroupClass(string $groupName): string
    {
        if (!isset(self::$groupClassMap[$groupName])) {
            throw new \LogicException(sprintf('Entity class for group "%s" is unknown. Make sure fixtures creating this group run before and call createMany().', $groupName));
        }

        return self::$groupClassMap[$groupName];
    }

    /**
     * Build references index for given group.
     *
     * @param string $groupName Group name
     */
    private function buildReferencesIndex(string $groupName): void
    {
        $class = $this->getGroupClass($groupName);

        $this->referencesIndex[$groupName] = [];

        for ($i = 0; $i < 10000; ++$i) {
            $name = sprintf('%s_%d', $groupName, $i);

            if ($this->hasReference($name, $class)) {
                $this->referencesIndex[$groupName][] = $name;
            } else {
                if ($i > 0) {
                    break;
                }
            }
        }
    }
}
