<?php

namespace App\Domain;

use React\Filesystem\Filesystem;
use React\Promise\PromiseInterface;

class FileReader
{
    private $content;
    private $filename;

    public function __construct(Filesystem $filesystem, string $filename)
    {
        $this->filename = $filename;
        $this->content = $filesystem->getContents($filename);
    }

    public function getContent() : PromiseInterface
    {
        return $this->content;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }
}
