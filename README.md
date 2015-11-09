HTTP Signatures Guzzle 6
========================

Guzzle 6 support for 99designs http-signatures library

[![Build Status](https://travis-ci.org/99designs/http-signatures-guzzlehttp.svg)](https://travis-ci.org/99designs/http-signatures-guzzlehttp)

Adds [99designs/http-signatures][99signatures] support to Guzzle 6.  


Older Guzzle Versions
---------------------
For Guzzle 4 & 5 use the `v1.x` release of this repo.
For Guzzle 3 see the [99designs/http-signatures-guzzle][99signatures-guzzle] repo.


Signing with Guzzle 6
---------------------

This library includes support for automatically signing Guzzle requests using Middleware.

You can use `GuzzleHttpSignatures::defaultHandlerFromContext` to easily create the default Guzzle handler with the 
middleware added to sign every request.

```php
use GuzzleHttp\Client;
use HttpSignatures\Context;
use HttpSignatures\GuzzleHttpSignatures;

require __DIR__ . "/../vendor/autoload.php";

$context = new Context([
    'keys' => ['examplekey' => 'secret-key-here'],
    'algorithm' => 'hmac-sha256',
    'headers' => ['(request-target)', 'date'],
]);

$handlerStack = GuzzleHttpSignatures::defaultHandlerFromContext($context);
$client = new Client(['handler' => $handlerStack]);

// The below will now send a signed request to: http://example.org/path?query=123
$response = $client->get("http://www.example.com/path?query=123", ['headers' => ['date' => 'today']]);
```

Or if you're creating a custom `HandlerStack` you can add the Middleware yourself:

```php
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
```


## Contributing

Pull Requests are welcome.

[99signatures]: https://github.com/99designs/http-signatures-php
[99signatures-guzzle]: https://github.com/99designs/http-signatures-guzzle
