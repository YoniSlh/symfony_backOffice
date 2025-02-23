<?php

namespace App\Tests;

use App\Entity\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testCreationProduit(): void
    {
        $product = new Product();
        $product->setName("VTT test");
        $product->setDescription("Test description.");
        $product->setPrice(1200);

        $this->assertEquals("VTT test", $product->getName());
        $this->assertEquals("Test description.", $product->getDescription());
        $this->assertEquals(1200, $product->getPrice());
    }
}
