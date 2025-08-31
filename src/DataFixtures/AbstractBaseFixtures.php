<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

/**
 * Bazowa klasa dla fixtures – kompatybilna z doctrine/data-fixtures 2.x
 * (getReference/hasReference wymagają 2 parametrów: name + class).
 */
abstract class AbstractBaseFixtures extends Fixture
{
    /** @var Generator|null */
    protected ?Generator $faker = null;

    /** @var ObjectManager|null */
    protected ?ObjectManager $manager = null;

    /**
     * Indeks nazw referencji po grupach, np. 'users' => ['users_0','users_1',...]
     * @var array<string, array<int, string>>
     */
    private array $referencesIndex = [];

    /**
     * Mapa "nazwa grupy" => "FQCN encji".
     * Statyczna, aby była widoczna między różnymi klasami fixtures.
     *
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

    /**
     * Tworzy wiele encji i zapisuje referencje pod nazwą {groupName}_{i}.
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

            // zapamiętaj klasę encji dla tej grupy
            self::$groupClassMap[$groupName] = \get_class($entity);

            // lokalny indeks (użyteczne przy losowaniu w tej samej klasie)
            $this->referencesIndex[$groupName][] = $refName;
        }
    }

    /** @return class-string */
    private function getGroupClass(string $groupName): string
    {
        if (!isset(self::$groupClassMap[$groupName])) {
            throw new \LogicException(sprintf(
                'Entity class for group "%s" is unknown. Make sure fixtures creating this group run before and call createMany().',
                $groupName
            ));
        }

        return self::$groupClassMap[$groupName];
    }

    /**
     * Buduje indeks nazw referencji dla danej grupy,
     * skanując nazwy {groupName}_{i} i sprawdzając hasReference($name, $class).
     */
    private function buildReferencesIndex(string $groupName): void
    {
        $class = $this->getGroupClass($groupName);

        $this->referencesIndex[$groupName] = [];

        // skanuj ciąg 0..N-1 – createMany tworzy ciągłe indeksy
        for ($i = 0; $i < 10000; $i++) {
            $name = sprintf('%s_%d', $groupName, $i);

            // Uwaga: w Twojej wersji wymagany jest 2. parametr $class
            if ($this->hasReference($name, $class)) {
                $this->referencesIndex[$groupName][] = $name;
            } else {
                // jeśli trafimy pierwszą „dziurę” po znalezionych, kończymy
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

        // Uwaga: w Twojej wersji wymagany jest 2. parametr $class
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
