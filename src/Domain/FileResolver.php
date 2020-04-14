<?php

namespace App\Domain;

use React\Filesystem\Filesystem;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;
use League\CommonMark\GithubFlavoredMarkdownConverter;

class FileResolver
{

    private $filename;
    private $filesystem;

    public function __construct(Filesystem $filesystem, string $filename)
    {
        $this->filename = $filename;
        $this->filesystem = $filesystem;
    }


    public function toHtml(): PromiseInterface
    {
        $deferred = new Deferred();

        $filename = $this->filename . '.html';

        $this->filesystem->file($filename)
            ->exists()
            ->then(function () use ($filename, &$deferred) {
                return $this->filesystem->getContents($filename)
                    ->then(function ($c) use (&$deferred) {
                        if (!empty($c)) {
                            $deferred->resolve($c);
                        }
                    });
            }, function () use (&$deferred) {
                $filename = $this->filename . '.md';
                return $this->filesystem->getContents($filename)
                    ->then(function ($c) use (&$deferred) {
                        if (!empty($c)) {
                            $converter = new GithubFlavoredMarkdownConverter([
                                'html_input' => 'strip',
                                'allow_unsafe_links' => false,
                            ]);
                            $deferred->resolve($converter->convertToHtml($c));
                        }
                    }, function () use (&$deferred) {
                        $deferred->resolve('');
                    });
            });

        return $deferred->promise();
    }
}
