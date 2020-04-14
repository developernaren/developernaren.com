<?php

declare(strict_types=1);

namespace App\Controller;

use App\Domain\FileResolver;
use React\Filesystem\Filesystem;
use React\Promise\FulfilledPromise;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

class DefaultController
{
    public function __invoke(Request $request, Filesystem $filesystem, KernelInterface $kernel)
    {

        $routeParams = $request->attributes->get('_route_params');
        $fileResolver = new FileResolver($filesystem, $kernel->getProjectDir() . '/Drift/views/' . $routeParams['page']);

        try {
            return $fileResolver
                ->toHtml()
                ->then(function ($content) {
                    $statusCode = 200;
                    if (empty($content)) {
                        $statusCode = 404;
                        $content = 'Sorry, I feel defeated. I tried to find what you are looking for. Believe me I tried.';
                    }

                    return new Response($content, $statusCode);
                });
        } catch (\Exception $exception) {

            return 'this is a test';
        }

    }
}
