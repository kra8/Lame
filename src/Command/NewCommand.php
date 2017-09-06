<?php

namespace Lame\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class NewCommand extends Command
{
    private $signature = 'new';

    private $arguments   = [
        'name' => InputArgument::REQUIRED,
    ];

    private $options = [
        'target' => InputOption::VALUE_REQUIRED,
    ];

    protected function configure()
    {
        $this->setName($this->signature);
        $this->setDescription('Create a new laravel and homestead project.');
        $this->addArgument('name', InputArgument::REQUIRED, 'Project name');
        $this->addOption('target', 't', InputOption::VALUE_REQUIRED, 'Select install laravel application version');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pwd         = $_SERVER['PWD'];
        $projectName = $input->getArgument('name');
        $projectPath = $pwd . '/' . $projectName;

        // if exist file
        if (file_exists($projectPath)) {
            $output->writeln('<error>ERROR:' . $projectName . ' is already exists.</error>');
            exit(1);
        }

        // start set up
        $output->writeln('<info>Start setup...</info>');

        // Create project folder
        mkdir($projectPath);

        // Create a laravel project
        $execute = 'laravel new ' . $projectName . '/app';
        shell_exec($execute);

        // Setup Homestead
        chdir($projectPath);

        $execute = 'composer require laravel/homestead --dev';
        shell_exec($execute);

        $execute = 'php vendor/bin/homestead make';
        shell_exec($execute);

        chdir($pwd);

        $output->writeln('<info>Complete.</info>');
        $output->writeln('<comment>You should editing Homestead.yaml, then run the "vagrant up".</comment>');
    }

}
