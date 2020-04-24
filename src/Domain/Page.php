<?php

namespace App\Domain;

use League\CommonMark\GithubFlavoredMarkdownConverter;

class Page
{
    private $parser;
    private $layoutContent;
    private $filename;

    public function __construct(MetaParser $parser, string $layoutContent, string $filename)
    {
        $this->parser = $parser;
        $this->layoutContent = $layoutContent;
        $this->filename = $filename;
    }

    public function toHtml(): string
    {
        $content = $this->parser->getBody();
        if(strpos($this->filename, '.md')) {
            $converter = new GithubFlavoredMarkdownConverter([
                'html_input' => 'strip',
                'allow_unsafe_links' => false,
            ]);
            $content = $converter->convertToHtml($content);
        }

        $replaces = [
            '{title}' => $this->parser->getTitle(),
            '{description}' => $this->parser->getDescription(),
            '{content}' => $content,
        ];

        $content = $this->layoutContent;
        foreach ($replaces as $key => $replace) {
            $content = str_replace($key, $replace, $content);
        }

        return  $content;
    }
}
