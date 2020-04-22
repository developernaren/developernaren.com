<?php

namespace App\Domain;

class MetaParser
{
    private $finalCollection = [];

    public function __construct(string $content)
    {
        $attributes = explode(PHP_EOL, $content);
        foreach (array_filter($attributes) as $attr) {
            [$key, $value] = explode(':', $attr);
            $this->finalCollection[trim($key)] = trim($value);
        }
    }

    public function getLayout(): ?string
    {
        return $this->getKey('layout');
    }


    private function getKey($key): ?string
    {
        return $this->finalCollection[$key] ?? null;
    }
}
