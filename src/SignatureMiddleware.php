<?php

namespace HttpSignatures\GuzzleHttp;

use GuzzleHttp\Message\RequestInterface;
use HttpSignatures\Context;

class SignatureMiddleware
{
    /**
     * The context instance.
     *
     * @var \HttpSignatures\Context
     */
    private $context;

    /**
     * Create a new signature middleware instance.
     *
     * @param \HttpSignatures\Context $context
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    /**
     * Handle before request.
     *
     * @param \GuzzleHttp\Message\RequestInterface $request
     */
    public function onBefore(RequestInterface $request)
    {
        $this->context->signer()->sign(new Message($request));
    }

    /**
     * Called when the middleware is handled.
     *
     * @param callable $handler
     *
     * @return \Closure
     */
    public function __invoke(callable $handler)
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            if (isset($options['auth']) && $options['auth'] == 'http-signatures') {
                $request = $this->onBefore($request);
            }

            return $handler($request, $options);
        };
    }
}
