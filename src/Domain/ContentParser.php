<?php


namespace App\Domain;


use League\CommonMark\GithubFlavoredMarkdownConverter;
use React\Filesystem\Filesystem;
use React\Promise\PromiseInterface;

class ContentParser
{
    private $content;
    private $reader;

    public function __construct(FileReader $reader)
    {
        $this->reader = $reader;
        $this->content = $reader->getContent();
    }

    public function getContent() : PromiseInterface
    {
        return $this->content;
    }

    public function getMetaInfo(): PromiseInterface
    {
        return $this->getContent()->then(function ($content){

            $endOfMeta = strpos($content, '</draft>');
            $metaContent = substr($content, 7, ($endOfMeta-8));
            $body = substr($content, $endOfMeta + 8);

            return new MetaParser($metaContent, $body);
        });
    }

    public function getHtml(): PromiseInterface
    {
        $isMd =  strpos($this->reader->getFilename(), '.md') !== false;
        $content =  $this->getContent();

        if(!$isMd) {
            return  $content;
        }

        return $content->then(function ($content){
            $converter = new GithubFlavoredMarkdownConverter([
                'html_input' => 'strip',
                'allow_unsafe_links' => false,
            ]);

            return $converter->convertToHtml($content);
        });
    }
}
