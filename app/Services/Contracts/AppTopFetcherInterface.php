<?php

namespace App\Services\Contracts;


interface AppTopFetcherInterface
{
    public function fetchAndStore(string $date): array;
}

