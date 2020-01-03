<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CollectionController extends BaseController
{
    public function practice1()
    {
        echo sprintf("Price of lamp and wallet is: %s$", $this->computePriceLampsAndWallets($this->products));
    }

    public function practice3()
    {
        echo sprintf("Converting binary value %s to decimal is: %s", $this->binaryValue, $this->binaryToDecimal($this->binaryValue));
    }

    private function computePriceLampsAndWallets($products)
    {
        $totalCost = 0;

        // Loop over every product
        foreach ($products as $product) {
            $productType = $product['product_type'];

            // If the product is a lamp or wallet...
            if ($productType == 'Lamp' || $productType == 'Wallet') {
                // Loop over the variants and add up their prices
                foreach ($product['variants'] as $productVariant) {
                    $totalCost += $productVariant['price'];
                }
            }
        }

        return $totalCost;
    }

    private function binaryToDecimal($binary)
    {
        $total = 0;
        $exponent = strlen($binary) - 1;

        for ($i = 0; $i < strlen($binary); $i++) {
            $decimal = $binary[$i] * (2 ** $exponent);
            $total += $decimal;
            $exponent--;
        }

        return $total;
    }
}
