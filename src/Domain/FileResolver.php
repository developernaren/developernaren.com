<?php

namespace App\Domain;

use React\Filesystem\Filesystem;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;

class FileResolver
{

    private $filename;
    private $filesystem;

    public function __construct(Filesystem $filesystem, string $filename)
    {
        $this->filename = $filename;
        $this->filesystem = $filesystem;
    }

    public function getFilename(): PromiseInterface
    {
        $deferred = new Deferred();
        $filename = $this->filename . '.html';
        $this->filesystem->file($filename)
            ->exists()
            ->then(function () use ($filename, &$deferred) {
                $deferred->resolve($filename);
            }, function () use (&$deferred) {
                $filename = $this->filename . '.md';
                return $this->filesystem->file($filename)
                    ->exists()
                    ->then(function ($c) use (&$deferred, $filename) {
                        $deferred->resolve($filename);
                    }, function () use (&$deferred) {
                        $deferred->resolve('');
                    });
            });

        return $deferred->promise();
    }
}
