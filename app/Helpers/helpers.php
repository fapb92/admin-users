<?php

use Illuminate\Support\Str;

function create_url($baseUrl, $endpoint)
{
    $baseUrl = Str::endsWith($baseUrl, '/') ? $baseUrl : "{$baseUrl}/";
    $endpoint = Str::startsWith($endpoint, '/') ? Str::replaceFirst('/', '', $endpoint) : $endpoint;

    return "{$baseUrl}{$endpoint}";
}
