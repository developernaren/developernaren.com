<?php

declare(strict_types=1);

namespace App\Controller;

use App\Domain\ContentParser;
use App\Domain\FileReader;
use App\Domain\FileResolver;
use React\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

class DefaultController
{
    public function __invoke(Request $request, Filesystem $filesystem, KernelInterface $kernel)
    {
        $layoutFile = $kernel->getProjectDir() . '/Drift/views/layouts/index.html';
        $routeParams = $request->attributes->get('_route_params');
        $layoutReader = new FileReader($filesystem, $layoutFile);

        return $layoutReader->getContent()
            ->then(function ($layoutContent) use ($filesystem, $kernel, $routeParams) {

                $fileResolver = new FileResolver($filesystem, $kernel->getProjectDir() . '/Drift/views/' . $routeParams['page']);
                return $fileResolver
                    ->getFilename()
                    ->then(function ($filename) use ($filesystem, $layoutContent) {
                        $reader = new ContentParser(new FileReader($filesystem, $filename));
                        return $reader->getHtml()
                            ->then(function ($content) use($layoutContent) {
                                $statusCode = 200;
//                        if (empty($content)) {
//                            $statusCode = 404;
//                            $content = 'Sorry, I feel defeated. I tried to find what you are looking for. Believe me I tried.';
//                        }

                                return new Response(str_replace('{content}', $content, $layoutContent), $statusCode);
                            });
                    });
            });

    }
}
