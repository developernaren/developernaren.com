<?php

namespace App\Commands;

use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use React\Filesystem\Filesystem;
use React\Stream\WritableStreamInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use function Clue\React\Block\await;

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

        $filename = $this->kernel->getProjectDir() . '/test.txt';
        $file = $this->filesystem->file($filename);


        $promise = $file->exists()
            ->then(function () use ($file){
                $file->remove();
                return $file->open('cw')->then(function ($stream) use ($file){
                    $stream->write('this is the best things that has ever');
                    return $file->close();
                });
            });

        await($promise, $this->loop);

        return 0;
    }
}
