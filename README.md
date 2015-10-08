# HTTP Signatures Guzzle

Guzzle support for 99designs http-signatures library.

[![Build Status](https://travis-ci.org/99designs/http-signatures-guzzlehttp.svg)](https://travis-ci.org/99designs/http-signatures-guzzlehttp)

Adds [99designs/http-signatures][99signatures] support to Guzzle.
For Guzzle 3 see the [99designs/http-signatures-guzzle][99signatures-guzzle] repo.

## Signing with Guzzle

This library includes support for automatically signing Guzzle requests using an event subscriber.

```php
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use HttpSignatures\GuzzleHttp\SignatureMiddleware;

$stack = HandlerStack::create();
$stack->push(new SignatureMiddleware([
  'keys' => ['examplekey' => 'secret-key-here'],
  'algorithm' => 'hmac-sha256',
  'headers' => ['(request-target)', 'Date', 'Accept'],
]));

$client = new Client([
  'base_uri' => 'http://example.org'
  'handler' => $stack,
]);

// The below will now send a signed request to: http://example.org/path?query=123
$client->get('/path?query=123', [
  'headers' => [
    'Date' => 'Wed, 30 Jul 2014 16:40:19 -0700',
    'Accept' => 'llamas',
  ],
]);
```

## Contributing

Pull Requests are welcome.

[99signatures]: https://github.com/99designs/http-signatures-php
[99signatures-guzzle]: https://github.com/99designs/http-signatures-guzzle

## License

HTTP Signatures is licensed under [The MIT License (MIT)](LICENSE).
