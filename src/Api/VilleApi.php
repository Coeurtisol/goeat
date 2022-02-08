<?php

namespace App\Api;

use Symfony\Component\HttpClient\HttpClient;

class VilleApi
{
    static public function getVilles(): array
    {
        $client = HttpClient::create();
        $response = $client->request(
            'GET',
            'https://geo.api.gouv.fr/communes'
        );

        $content = $response->getContent();
        $content = $response->toArray();
        // dd($content);

        return $content;
    }
}
