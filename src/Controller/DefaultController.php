<?php

declare(strict_types=1);

namespace App\Controller;

use App\Domain\CacheNameResolver;
use App\Domain\ContentParser;
use App\Domain\FileReader;
use App\Domain\FileResolver;
use App\Domain\MetaParser;
use App\Domain\Page;
use React\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

class DefaultController
{
    public function __invoke(Request $request, Filesystem $filesystem, KernelInterface $kernel)
    {
        $routeParams = $request->attributes->get('_route_params');
        $fileResolver = new FileResolver($filesystem, $kernel->getProjectDir() . '/Drift/draft/pages/' . $routeParams['page']);

        return $fileResolver->getFilename()
            ->then(function ($filename) use ($filesystem, $kernel) {
                $reader = new FileReader($filesystem, $filename);
                $contentParser = new ContentParser($reader);

                return $contentParser->getMetaInfo()->then(function (MetaParser $meta) use ($filesystem, $kernel, $filename) {
                    $layoutReader = new FileReader($filesystem, $kernel->getProjectDir() . '/Drift/draft/layouts/' . $meta->getLayout());
                    return $layoutReader->getContent()->then(function ($layoutContent) use ($meta, $filesystem, $filename) {
                        $pageContent = new Page($meta, $layoutContent, $filename);
                        $content = $pageContent->toHtml();

                        return new Response($content);
                    });
                });
            });
    }
}
