<?php

namespace App\Service;

class CsvExporter
{
    public function export(array $products): string
    {
        $csvData = "name,description,price\n";

        foreach ($products as $product) {
            $csvData .= sprintf(
                "\"%s\",\"%s\",%s\n",
                $product->getName(),
                $product->getDescription(),
                $product->getPrice()
            );
        }

        return $csvData;
    }
}
