<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use HttpSignatures\Context;
use HttpSignatures\GuzzleHttpSignatures;

require __DIR__ . "/../vendor/autoload.php";

$context = new Context([
    'keys' => ['examplekey' => 'secret-key-here'],
    'algorithm' => 'hmac-sha256',
    'headers' => ['(request-target)', 'date'],
]);

$handlerStack = new HandlerStack();
$stack->setHandler(new CurlHandler());
$stack->push(GuzzleHttpSignatures::middlewareFromContext($this->context));
$stack->push(Middleware::history($this->history));
$client = new Client(['handler' => $handlerStack]);

// The below will now send a signed request to: http://example.org/path?query=123
$response = $client->get("http://www.example.com/path?query=123", ['headers' => ['date' => 'today']]);
