<?php


namespace App\Commands;


use React\EventLoop\LoopInterface;
use React\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpKernel\KernelInterface;
use function Clue\React\Block\await;
use function Clue\React\Block\awaitAll;

class CopyImages extends Command
{

    protected static $defaultName = 'image';

    private $filesystem;
    private $kernel;
    private $loop;
    private $baseUrl = 'http://fakedomain.com';
    private $imagesToCopy = [];

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
        $directory = $this->kernel->getProjectDir() . '/build';

        $extractBgImage = $this->filesystem->dir($directory)
            ->lsRecursive()
            ->then(function ($nodes) {
                foreach ($nodes as $node) {
                    $filename = (string)$node;
                    if ($this->isHtml($filename)) {

                        return $this->filesystem->getContents($filename)
                            ->then(function ($content) {

                                $crawler = new Crawler($content, $this->baseUrl);
                                $divs = $crawler->filter('div');
                                $bodies = $crawler->filter('body');

                                foreach ($divs as $div) {
                                    if (!empty($div->getAttribute('style'))) {
                                        $this->saveBackgroundImage($div);
                                    }
                                }

                                foreach ($bodies as $div) {
                                    if (!empty($div->getAttribute('style'))) {
                                        $this->saveBackgroundImage($div);
                                    }
                                }
                            });


                    }
                }
            }, function () {
                echo 'error reading dir';
            });

        await($extractBgImage, $this->loop);


        $extractImgTag = $this->filesystem->dir($directory)
            ->lsRecursive()
            ->then(function ($nodes) {

                foreach ($nodes as $node) {
                    $filename = (string)$node;
                    if ($this->isHtml($filename)) {

                        return $this->filesystem->getContents($filename)
                            ->then(function ($content) {
                                $crawler = new Crawler($content, $this->baseUrl);
                                $images = $crawler->filter('img')->images();
                                foreach ($images as $image) {
                                    $imageUrl = $image->getUri();
                                    $imagePath = str_replace($this->baseUrl, '', $imageUrl);
                                    $this->addImages($imagePath);
                                }
                            });
                    }
                }
            }, function () {
                echo 'error reading dir';
            });


        await($extractImgTag, $this->loop);

        $promises = [];
        foreach($this->imagesToCopy as $image) {
            $promises[] = $this->saveImage($image);
        }

        awaitAll($promises, $this->loop);

        return 0;

    }

    private function isHtml($filename)
    {
        return $this->endsWith($filename, '.html');
    }

    private function endsWith($haystack, $needle)
    {
        return (strlen($haystack) - strlen($needle)) === strpos($haystack, $needle);
    }

    private function startsWith($haystack, $needle)
    {
        return strpos($haystack, $needle) === 0;
    }

    private function saveBackgroundImage($node)
    {
        $styles = explode(';', $node->getAttribute('style'));
        $backgroundImages = array_filter($styles, function ($style) {
            return strpos($style, 'background-image') !== false;
        });

        foreach ($backgroundImages as $image) {
            $image = str_replace('background-image', '', $image);
            $image = str_replace(':', '', $image);
            $image = str_replace('url', '', $image);
            $image = str_replace('(', '', $image);
            $image = str_replace(')', '', $image);
            $image = str_replace("'", '', $image);
            $image = str_replace('"', '', $image);
            $this->addImages($image);
        }
    }

    public function addImages(string $image)
    {
        if (!in_array($image, $this->imagesToCopy) && !$this->startsWith($image, 'http')) {
            array_push($this->imagesToCopy, trim($image));
        }

    }

    public function saveImage($imagePath)
    {
        $imagePath = trim($imagePath);
        $fromPath = $this->kernel->getProjectDir() . $imagePath;
        $toPath = $this->kernel->getProjectDir() . '/build' . $imagePath;

        $from = $this->filesystem->file($fromPath);
        $to = $this->filesystem->file($toPath);


        $imageFolder = str_replace(strrchr($toPath, '/'), '', $toPath);

        $createFolder = $this->filesystem->dir($imageFolder)->createRecursive('rwxrwx---')
            ->then(function () {

            }, function () {

            });
        await($createFolder, $this->loop);

        return $from->copy($to);
    }
}
