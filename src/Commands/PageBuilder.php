<?php

namespace App\Commands;

use App\Domain\ContentParser;
use App\Domain\FileReader;
use App\Domain\MetaParser;
use App\Domain\Page;
use React\EventLoop\LoopInterface;
use React\Filesystem\Filesystem;
use React\Promise\PromiseInterface;
use React\Stream\WritableStreamInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use function Clue\React\Block\await;
use function Clue\React\Block\awaitAll;

class PageBuilder extends Command
{

    protected static $defaultName = 'build';

    private $filesystem;
    private $kernel;
    private $loop;

    public function __construct(Filesystem $filesystem, KernelInterface $kernel, LoopInterface $loop)
    {
        $this->filesystem = $filesystem;
        $this->kernel = $kernel;
        $this->loop = $loop;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Builds the pages static pages');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $dir = $this->filesystem->dir($this->kernel->getProjectDir() . '/Drift/draft/pages');

        $checkDir = $dir->lsRecursive()->then(function ($nodes) {
            $promises = [];
            foreach ($nodes as $node) {

                $promises[] = $this->filesystem->dir($this->getBuildFolder((string)$node))
                    ->create('rwxrwx---')
                    ->then(function () {
                        echo "created" . PHP_EOL;
                    });
            }

            try {
                awaitAll($promises, $this->loop);
            } catch (\Exception $exception) {
                echo $exception->getMessage(). PHP_EOL;
            }
        });

        await($checkDir, $this->loop);

        $writeFiles = $dir->lsRecursive()->then(function ($nodes) {
            foreach ($nodes as $node) {
                $filename = (string)$node;
                if ($this->isHtml($filename) || $this->isMd($filename)) {
                    try {
                        await($this->buildPage($filename), $this->loop);
                    } catch (\Exception $exception) {
                        echo $exception->getMessage() .  $exception->getFile() . $exception->getLine().PHP_EOL;
                    }
                }
            }

        });


        await($writeFiles, $this->loop);

        return 0;
    }

    private function getBuildFolder($filename)
    {
        $folderName =  str_replace('.md', '', str_replace('.html', '', str_replace('/Drift/draft/pages/', '/build/', $filename)));
        if($this->endsWith($folderName, 'index')) {
            return substr_replace($folderName, '', -5, 5);
        }

        return $folderName;
    }

    private function getBuildFilename($filename)
    {
        $filename =  str_replace('/Drift/draft/pages/', '/build/', str_replace('.md','.html',$filename));
        $filename = str_replace('.html', '/index.html', $filename);

        if($this->endsWith($filename, 'index/index.html')) {
            return substr_replace($filename, '', -11, 6);
        }

        return $filename;

    }

    private function isMd($filename)
    {
        return $this->endsWith($filename, '.md');
    }

    private function isHtml($filename)
    {
        return $this->endsWith($filename, '.html');
    }

    private function endsWith($haystack, $needle)
    {
        return (strlen($haystack) - strlen($needle)) === strpos($haystack, $needle);
    }

    private function buildPage($filename): PromiseInterface
    {
        $reader = new FileReader($this->filesystem, $filename);
        $contentParser = new ContentParser($reader);
        return $contentParser->getMetaInfo()->then(function (MetaParser $meta) use ($filename) {
            $layoutReader = new FileReader($this->filesystem, $this->kernel->getProjectDir() . '/Drift/draft/layouts/' . $meta->getLayout());
            return $layoutReader->getContent()->then(function ($layoutContent) use ($meta, $filename) {
                $pageContent = new Page($meta, $layoutContent, $filename);
                $finalFileName = $this->getBuildFilename($filename);
                echo $finalFileName . PHP_EOL;
                $file = $this->filesystem->file($finalFileName);
                return $file->open('cwt')
                    ->then(function (WritableStreamInterface $stream) use ($pageContent, $file){
                        $stream->write($pageContent->toHtml());
                        $stream->end();
                        return $file->close();
                    }, function (){
                        echo "Could no write file";
                    });
            });
        });

    }
}
