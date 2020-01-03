<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RefactorCollectionController extends BaseController
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
        return collect($products)->filter(function ($product) {
            return collect(['Lamp', 'Wallet'])->contains($product['product_type']);
        })->flatMap(function ($product) {
            return $product['variants'];
        })->sum('price');
    }

    private function binaryToDecimal($binary)
    {
        return collect(str_split($binary))
            ->reverse()
            ->values()
            ->map(function ($column, $exponent) {
                return $column * (2 ** $exponent);
            })->sum();
    }
}
