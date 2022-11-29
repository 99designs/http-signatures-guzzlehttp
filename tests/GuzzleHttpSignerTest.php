<?php

namespace HttpSignatures\Test;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use HttpSignatures\Context;
use HttpSignatures\GuzzleHttpSignatures;
use PHPUnit\Framework\TestCase;

class GuzzleHttpSignerTest extends TestCase
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var
     */
    private $history = [];

    public function setUp() : void
    {
        $this->context = new Context([
            'keys' => ['pda' => 'secret'],
            'algorithm' => 'hmac-sha256',
            'headers' => ['(request-target)', 'date'],
        ]);

        $stack = new HandlerStack();
        $stack->setHandler(new MockHandler([
            new Response(200, ['Content-Length' => 0]),
        ]));
        $stack->push(GuzzleHttpSignatures::middlewareFromContext($this->context));
        $stack->push(Middleware::history($this->history));
        $this->client = new Client(['handler' => $stack]);
    }

    /**
     * test signing a message
     */
    public function testGuzzleRequestHasExpectedHeaders()
    {
        $this->client->get('/path?query=123', [
            'headers' => ['date' => 'today', 'accept' => 'llamas']
        ]);

        // get last request
        $message = end($this->history);
        /** @var Request $request */
        $request = $message['request'];
        /** @var Response $response */
        $response = $message['request'];

        $expectedString = implode(
            ',',
            [
                'keyId="pda"',
                'algorithm="hmac-sha256"',
                'headers="(request-target) date"',
                'signature="SFlytCGpsqb/9qYaKCQklGDvwgmrwfIERFnwt+yqPJw="',
            ]
        );

        $this->assertEquals(
            [$expectedString],
            $request->getHeader('Signature')
        );

        $this->assertEquals(
            ['Signature ' . $expectedString],
            $request->getHeader('Authorization')
        );
    }

    /**
     * test signing a message with a URL that doesn't contain a ?query
     */
    public function testGuzzleRequestHasExpectedHeaders2()
    {
        $this->client->get('/path', [
            'headers' => ['date' => 'today', 'accept' => 'llamas']
        ]);

        // get last request
        $message = end($this->history);
        /** @var Request $request */
        $request = $message['request'];
        /** @var Response $response */
        $response = $message['request'];

        $expectedString = implode(
            ',',
            [
                'keyId="pda"',
                'algorithm="hmac-sha256"',
                'headers="(request-target) date"',
                'signature="DAtF133khP05pS5Gh8f+zF/UF7mVUojMj7iJZO3Xk4o="',
            ]
        );

        $this->assertEquals(
            [$expectedString],
            $request->getHeader('Signature')
        );

        $this->assertEquals(
            ['Signature ' . $expectedString],
            $request->getHeader('Authorization')
        );
    }

    public function getVerifyGuzzleRequestVectors() {
        return [
            /* path, headers */
            ['/path?query=123', ['date' => 'today', 'accept' => 'llamas']],
            ['/path?z=zebra&a=antelope', ['date' => 'today']],
        ];
    }

    /**
     * @dataProvider getVerifyGuzzleRequestVectors
     * @param string $path
     * @param array $headers
     */
    public function testVerifyGuzzleRequest($path, $headers)
    {
        $this->client->get($path, ['headers' => $headers]);

        // get last request
        $message = end($this->history);
        /** @var Request $request */
        $request = $message['request'];
        /** @var Response $response */
        $response = $message['request'];

        $this->assertTrue($this->context->verifier()->isValid($request));
    }
}
