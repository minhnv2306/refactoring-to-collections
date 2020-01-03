<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
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

    public function practice4()
    {
        echo sprintf("Your Github score is: %s", $this->githubScore("minhnv2306"));
    }

    public function practice13()
    {
        echo sprintf("Ranking a Competition: <br>");

        $this->rank_scores($this->scores)->each(function ($item) {
            echo sprintf("Team %s, score: %s, ranking: %s <br> ", $item['team'], $item['score'], $item['rank']);
        });
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

    function githubScore($username)
    {
        return $this->fetchEvents($username)->pluck('type')->map(function ($eventType) {
            return $this->lookupEventScore($eventType);
        })->sum();
    }

    private function fetchEvents($username)
    {
        $url = "https://api.github.com/users/{$username}/events";
        $client = new Client();
        $res = $client->request('GET', $url);

        return collect(json_decode($res->getBody(), true));
    }

    private function lookupEventScore($eventType)
    {
        return collect([
            'PushEvent' => 5,
            'CreateEvent' => 4,
            'IssuesEvent' => 3,
            'CommitCommentEvent' => 2,
        ])->get($eventType, 1);
    }

    private function rank_scores($scores)
    {
        return collect($scores)
            ->pipe('assign_initial_rankings')
            ->pipe('adjust_rankings_for_ties')
            ->sortBy('rank');
    }
}
