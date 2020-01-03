<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CollectionController extends BaseController
{
    public function practice1()
    {
        echo sprintf("Price of lamp and wallet is: %s$", $this->computePriceLampsAndWallets($this->products));
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
}
