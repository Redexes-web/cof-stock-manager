<?php

namespace App\DataFixtures;

use App\Entity\CofStock;
use App\Entity\CofProduct;
use App\Entity\CofSupplier;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class StartFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $supplier1 = new CofSupplier();
        $supplier1->setName('Supplier 1');

        $supplier2 = new CofSupplier();
        $supplier2->setName('Supplier 2');

        $supplier3 = new CofSupplier();
        $supplier3->setName('Supplier 3');

        $manager->persist($supplier1);
        $manager->persist($supplier2);
        $manager->persist($supplier3);

        $product1 = new CofProduct();
        $product1->setName('Product 1');
        $product1->setPrice(10.5);
        
        $product2 = new CofProduct();
        $product2->setName('Product 2');
        $product2->setPrice(20.5);

        $product3 = new CofProduct();
        $product3->setName('Product 3');
        $product3->setPrice(30.5);

        $manager->persist($product1);
        $manager->persist($product2);
        $manager->persist($product3);

        $stock1 = new CofStock();
        $stock1->setStock(100);
        $stock1->setProduct($product1);
        $stock1->setSupplier($supplier1);

        $stock2 = new CofStock();
        $stock2->setStock(200);
        $stock2->setProduct($product2);
        $stock2->setSupplier($supplier1);

        $stock3 = new CofStock();
        $stock3->setStock(300);
        $stock3->setProduct($product3);
        $stock3->setSupplier($supplier1);

        $stock4 = new CofStock();
        $stock4->setStock(400);
        $stock4->setProduct($product1);
        $stock4->setSupplier($supplier2);

        $stock5 = new CofStock();
        $stock5->setStock(500);
        $stock5->setProduct($product2);
        $stock5->setSupplier($supplier2);

        $manager->persist($stock1);
        $manager->persist($stock2);
        $manager->persist($stock3);
        $manager->persist($stock4);
        $manager->persist($stock5);

        $manager->flush();
        
        

        $manager->flush();
    }
}
