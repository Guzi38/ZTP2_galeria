<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

abstract class AbstractBaseFixtures extends Fixture
{
    protected ?Generator $faker = null;

    protected ?ObjectManager $manager = null;

    /**
     * @var array<string, array<int, string>>
     */
    private array $referencesIndex = [];

    /**
     * @var array<string, class-string>
     */
    private static array $groupClassMap = [];

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $this->faker = Factory::create();
        $this->loadData();
    }

    abstract protected function loadData(): void;

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

    /** @return class-string */
    private function getGroupClass(string $groupName): string
    {
        if (!isset(self::$groupClassMap[$groupName])) {
            throw new \LogicException(sprintf('Entity class for group "%s" is unknown. Make sure fixtures creating this group run before and call createMany().', $groupName));
        }

        return self::$groupClassMap[$groupName];
    }

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

    protected function getRandomReference(string $groupName): object
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

    /** @return object[] */
    protected function getRandomReferences(string $groupName, int $count): array
    {
        $result = [];
        while (count($result) < $count) {
            $result[] = $this->getRandomReference($groupName);
        }

        return $result;
    }
}
