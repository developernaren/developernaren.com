<?php


namespace App\Commands;


use React\EventLoop\LoopInterface;
use React\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use function Clue\React\Block\await;

class RemoveFile extends Command
{

    protected static $defaultName = 'remove';

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


        $promise = $this->filesystem->file($filename)->remove();
        await($promise, $this->loop);

        return 0;
    }
}
