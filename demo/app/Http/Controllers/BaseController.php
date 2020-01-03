<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BaseController extends Controller
{
    protected $products = [
        [
            "title"=> "Small Rubber Wallet",
            "product_type"=> "Wallet",
            "variants"=> [
                [ "title"=> "Blue", "price"=> 29.33 ],
                [ "title"=> "Turquoise", "price"=> 18.50 ]
            ]
        ],
        [
            "title"=> "Sleek Cotton Shoes",
            "product_type"=> "Shoes",
            "variants"=> [
                [ "title"=> "Sky Blue", "price"=> 20.00 ]
            ]
        ],
        [
            "title"=> "Intelligent Cotton Wallet",
            "product_type"=> "Wallet",
            "variants"=> [
                [ "title"=> "White", "price"=> 17.97 ]
            ]
        ],
        [
            "title"=> "Enormous Leather Lamp",
            "product_type"=> "Lamp",
            "variants"=> [
                [ "title"=> "Azure", "price"=> 65.99 ],
                [ "title"=> "Salmon", "price"=> 1.66 ]
            ]
        ],
    ];

    protected $binaryValue = '100110101';

    protected $scores = [
        ['score' => 76, 'team' => 'A'],
        ['score' => 62, 'team' => 'B'],
        ['score' => 82, 'team' => 'C'],
        ['score' => 86, 'team' => 'D'],
        ['score' => 91, 'team' => 'E'],
        ['score' => 67, 'team' => 'F'],
        ['score' => 67, 'team' => 'G'],
        ['score' => 82, 'team' => 'H'],
    ];

    protected $employees = [
        [
            'name' => 'John',
            'department' => 'Sales',
            'email' => 'john@example.com'
        ],
        [
            'name' => 'Jane',
            'department' => 'Marketing',
            'email' => 'jane@example.com'
        ],
        [
            'name' => 'Dave',
            'department' => 'Marketing',
            'email' => 'dave@example.com'
        ],
    ];
}
