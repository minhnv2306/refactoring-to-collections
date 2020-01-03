<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
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

    public function practice4()
    {
        echo sprintf("Your Github score is: %s", $this->githubScore("minhnv2306"));
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

    private function githubScore($username)
    {
        // Grab the events from the API, in the real world you'd probably use
        // Guzzle or similar here, but keeping it simple for the sake of brevity.
        $url = "https://api.github.com/users/{$username}/events";

        $client = new Client();
        $res = $client->request('GET', $url);
        $events = json_decode($res->getBody(), true);

        // Get all of the event types
        $eventTypes = [];

        foreach ($events as $event) {
            $eventTypes[] = $event['type'];
        }

        // Loop over the event types and add up the corresponding scores
        $score = 0;

        foreach ($eventTypes as $eventType) {
            switch ($eventType) {
                case 'PushEvent':
                    $score += 5;
                    break;
                case 'CreateEvent':
                    $score += 4;
                    break;
                case 'IssuesEvent':
                    $score += 3;
                    break;
                case 'CommitCommentEvent':
                    $score += 2;
                    break;
                default:
                    $score += 1;
                    break;
            }
        }

        return $score;
    }
}
