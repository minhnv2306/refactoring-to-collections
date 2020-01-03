<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RefactorCollectionController extends BaseController
{
    public function practice1()
    {
        echo sprintf("Price of lamp and wallet is: %s$", $this->computePriceLampsAndWallets($this->products));
    }

    private function computePriceLampsAndWallets($products)
    {
        return collect($products)->filter(function ($product) {
            return collect(['Lamp', 'Wallet'])->contains($product['product_type']);
        })->flatMap(function ($product) {
            return $product['variants'];
        })->sum('price');
    }
}
