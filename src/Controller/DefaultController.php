<?php

/*
 * This file is part of the DriftPHP package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Marc Morera <yuhu@mmoreram.com>
 */

declare(strict_types=1);

namespace App\Controller;

use Drift\Kernel;
use React\Filesystem\Filesystem;
use React\Promise\FulfilledPromise;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class DefaultController.
 *
 * You can run this action by making `curl` to /
 */
class DefaultController
{
    public function __invoke(Request $request, Filesystem $filesystem, KernelInterface $kernel)
    {
        return $filesystem->getContents($kernel->getProjectDir() . '/Drift/views/index.html')
            ->then(function ($content){
                return new Response($content);
            });
    }
}
