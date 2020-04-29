<?php

namespace App\Domain;

class MetaParser
{
    private $finalCollection = [];
    private $body;

    public function __construct(string $content, string $body)
    {
        $this->body = $body;
        $attributes = explode(PHP_EOL, $content);
        foreach (array_filter($attributes) as $attr) {
            [$key, $value] = explode(':', $attr);
            $this->finalCollection[trim($key)] = trim($value);
        }
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function getLayout(): ?string
    {
        return $this->getKey('layout');
    }

    public function getTitle():?string
    {
        return $this->getKey('title');
    }

    public function getDescription():?string
    {
        return $this->getKey('description');
    }

    public function getKey($key): ?string
    {
        return $this->finalCollection[$key] ?? null;
    }

    public function getExtraMetas(): array
    {
        $extraMetas = $this->finalCollection;
        unset($extraMetas['layout']);
        return  $this->finalCollection;
    }
}
