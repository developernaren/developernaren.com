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

        $content = str_replace('{content}', $content, $this->layoutContent);

        foreach ($this->parser->getExtraMetas() as $key => $meta) {
            $key = '{'. $key .'}';
            $content = str_replace($key, $meta, $content);
        }


        return  $content;
    }
}
