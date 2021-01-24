<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $numCategories = 5;

        for ($i = 1; $i <= $numCategories; $i++) {
            $category = new Category();
            $category->setName("category_" . $i);
            $category->setDescription("description category_" . $i);
            $manager->persist($category);
        }
        $manager->flush();
        $categoryRepo = $manager->getRepository(Category::class);
        $items = [
            [
                'name' => 'Producto 1',
                'category' => 1,
                'currency' => 'EUR',
                'price' => 30,
                'featured' => 1,

            ],
            [
                'name' => 'Producto 2',
                'category' => 2,
                'currency' => 'EUR',
                'price' => 50,
                'featured' => 0,

            ],
            [
                'name' => 'Producto 3',
                'category' => 2,
                'currency' => 'EUR',
                'price' => 150,
                'featured' => 1,

            ],
            [
                'name' => 'Producto 4',
                'category' => 3,
                'currency' => 'EUR',
                'price' => 60,
                'featured' => 0,

            ],
            [
                'name' => 'Producto 5',
                'category' => 3,
                'currency' => 'USD',
                'price' => 250,
                'featured' => 1,

            ],
            [
                'name' => 'Producto 6',
                'category' => 4,
                'currency' => 'USD',
                'price' => 100,
                'featured' => 1,

            ]
        ];

        foreach ($items as $item) {
            $category = $categoryRepo->find($item['category']);

            $product = new Product();
            $product->setName($item['name']);
            /** @var Category $category */
            $product->setCategory($category);
            $product->setCurrency($item['currency']);
            $product->setPrice($item['price']);
            $product->setFeatured($item['featured']);
            $manager->persist($product);
        }


        $manager->flush();
    }
}
