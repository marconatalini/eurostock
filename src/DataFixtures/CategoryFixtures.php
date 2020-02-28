<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    private $categories;

    public function __construct()
    {
        $this->categories = [
            ['usa e getta', 'Foto di scambio, cancellate il giorno seguente', 1, false],
            ['terzisti', 'Foto preparazione materiale, aggiungere un TAG con il nome fornitore o cliente', 20, false],
            ['prodotti', 'Foto di prodotti realizzati', 999, true],
        ];
    }

    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        foreach ($this->categories as $category) {
            $record = new Category();
            $record->setName($category[0]);
            $record->setDescription($category[1]);
            $record->setDaysBeforeDelete($category[2]);
            $record->setSafe($category[3]);
            $manager->persist($record);
        }

        $manager->flush();
    }
}
