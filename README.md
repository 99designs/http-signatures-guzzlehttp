HTTP Signatures Guzzle 3
===

Adds Guzzle 3 support to [99designs/http-signatures][99signatures]

Signing with Guzzle 3
---

This library includes support for automatically signing Guzzle requests using an event subscriber.

```php
use HttpSignatures\Guzzle\CreateRequestSubscriber;

$client = new \Guzzle\Http\Client('http://example.org');
$client->addSubscriber(new CreateRequestSubscriber($context));

// The below will now send a signed request to: http://example.org/path?query=123
$client->get('/path?query=123', array(
  'Date' => 'Wed, 30 Jul 2014 16:40:19 -0700',
  'Accept' => 'llamas',
))->send();
```

## Contributing

Pull Requests are welcome.

[99signatures]: https://github.com/99designs/http-signatures-php
