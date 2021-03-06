<?php

namespace Iulyanp\ElixirBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class ElixirCommand.
 */
class ElixirCommand extends ContainerAwareCommand
{
    /**
     * Configure.
     */
    public function configure()
    {
        $this->setName('elixir:init')
            ->setDescription('Init package.json and gulpfile.');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $this->systemCheck($output);

        $this->writeInfo($output, 'Installing laravel elixir...');

        $appDir = $container->getParameter('kernel.root_dir');
        $webDir = $container->getParameter('web_dir');
        $buildDir = $container->getParameter('build_dir');
        $assetsPath = $container->getParameter('assets_dir');

        $rootDir = $this->getRootDir($appDir);
        $publicPath = $this->getPublicPath($rootDir, $webDir);

        try {
            $fs = new Filesystem();

            $packageContent = $this->getPackageContent();
            $packagePath = sprintf('%s%s', $rootDir, 'package.json');
            $fs->touch($packagePath);
            $fs->dumpFile($packagePath, $packageContent);

            $gulpContent = $this->getGulpfileContent($publicPath, $buildDir, $assetsPath);
            $gulpfilePath = sprintf('%s%s', $rootDir, 'gulpfile.js');
            $fs->touch($gulpfilePath);
            $fs->dumpFile($gulpfilePath, $gulpContent);
        } catch (IOExceptionInterface $e) {
            $this->writeError($output, $e->getMessage());
        }
    }

    /**
     * @param OutputInterface $output
     * @param string          $error
     *
     * @return mixed
     */
    private function writeError(OutputInterface $output, $error)
    {
        return $output->writeln('<error>'.$error.'</error>');
    }

    /**
     * @param OutputInterface $output
     * @param string          $message
     *
     * @return mixed
     */
    private function writeInfo(OutputInterface $output, $message)
    {
        return $output->writeln(sprintf('<info>%s</info>', $message));
    }

    /**
     * @return string
     */
    private function getPackageContent()
    {
        return '{
  "private": true,
  "scripts": {
    "prod": "gulp --production",
    "dev": "gulp watch"
  },
  "author": "iulyanpopa@gmail.com",
  "devDependencies": {
    "gulp": "^3.9.1",
    "laravel-elixir": "^6.0.0-10",
    "gulp-sass": "^2.3.2"
  }
}';
    }

    /**
     * @param string $webDir
     * @param string $buildDir
     * @param string $assetsPath
     *
     * @return string
     */
    private function getGulpfileContent($webDir, $buildDir, $assetsPath = 'app/Resources/assets')
    {
        return sprintf("// Import Elixir.
var elixir = require('laravel-elixir');

// Configure Elixir.
elixir.config.publicPath = '%s';
//elixir.config.appPath = 'src';
elixir.config.versioning.buildFolder = '%s';
elixir.config.assetsPath = '%s';

// Set up Elixir tasks.
elixir(function(mix) {

    mix.sass('app.scss')
        .version(['%s/css/app.css']);

})", $webDir, $buildDir, $assetsPath, $webDir);
    }

    /**
     * @param OutputInterface $output
     */
    private function systemCheck(OutputInterface $output)
    {
        $this->writeInfo($output, 'Checking requirements...');

        $errors = [];
        $checks = [];
        $requirements = ['node', 'npm', 'gulp'];
        foreach ($requirements as $requirement) {
            if (!$checks[$requirement] = exec($requirement.' -v')) {
                $errors[$requirement] = sprintf('You should first install `%s`.', $requirement);
            }
        }

        if (!empty($errors)) {
            foreach ($errors as $error) {
                $this->writeError($output, $error);
            }
            die;
        }

        foreach ($checks as $key => $check) {
            $output->writeln(sprintf('%s %s: %s', '<info>[OK]</info>', $key, $check));
        }
    }

    /**
     * @param $appDir
     *
     * @return string
     */
    private function getRootDir($appDir)
    {
        return sprintf('%s%s%s%s', $appDir, DIRECTORY_SEPARATOR, '..', DIRECTORY_SEPARATOR);
    }

    /**
     * @param $rootDir
     * @param $webDir
     *
     * @return mixed
     */
    private function getPublicPath($rootDir, $webDir)
    {
        return str_replace($rootDir, '', $webDir);
    }
}
