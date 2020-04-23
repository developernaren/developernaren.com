<?php

namespace App\Domain;

class Page
{
    private $parser;
    private $layoutContent;

    public function __construct(MetaParser $parser, string $layoutContent)
    {
        $this->parser = $parser;
        $this->layoutContent = $layoutContent;
    }

    public function toHtml(): string
    {
        $replaces = [
            '{title}' => $this->parser->getTitle(),
            '{description}' => $this->parser->getDescription(),
            '{content}' => $this->parser->getBody(),
        ];

        $content = $this->layoutContent;
        foreach ($replaces as $key => $replace) {
            $content = str_replace($key, $replace, $content);
        }

        return  $content;
    }
}
