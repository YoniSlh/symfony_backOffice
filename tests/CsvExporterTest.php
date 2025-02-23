<?php

namespace App\Tests\Service;

use App\Entity\Product;
use App\Service\CsvExporter;
use PHPUnit\Framework\TestCase;

class CsvExporterTest extends TestCase
{
    public function testExport(): void
    {
        $product1 = (new Product())
            ->setName('Vélo de route')
            ->setDescription('Un vélo rapide test')
            ->setPrice(1200);

        $product2 = (new Product())
            ->setName('VTT')
            ->setDescription('Vélo tout terrain test')
            ->setPrice(1500);

        $exporter = new CsvExporter();
        $csv = $exporter->export([$product1, $product2]);

        $expectedCsv = "name,description,price\n";
        $expectedCsv .= "\"Vélo de route\",\"Un vélo rapide test\",1200\n";
        $expectedCsv .= "\"VTT\",\"Vélo tout terrain test\",1500\n";

        $this->assertEquals($expectedCsv, $csv);
    }
}
