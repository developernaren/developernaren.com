<?php

namespace App\Domain;

class CacheNameResolver
{
    private $filename;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    public function getName(): string
    {
        return $this->filename . '.cache';
    }
}
