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
            'lavorazione',
            'pacco',
            'imballo',
            'viv',
            'prodotto',
            'difetto',
            'documento',
        ];
    }

    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        foreach ($this->categories as $category) {
            $record = new Category();
            $record->setName($category);
            $manager->persist($record);
        }

        $manager->flush();
    }
}
