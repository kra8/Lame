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
        $execute = $_SERVER['HOME'] . '/.composer/vendor/bin/laravel new ' . $projectName . '/app';
        shell_exec($execute);

        // Setup Homestead
        chdir($projectPath);

        $execute = 'composer require laravel/homestead --dev';
        shell_exec($execute);

        $execute = 'php vendor/bin/homestead make';
        shell_exec($execute);

        // buckup setting file
        $execute = 'cp Homestead.yaml Homestead.yaml.org';
        shell_exec($execute);

        // setting homestead
        $yaml    = file_get_contents('Homestead.yaml');
        $yaml    = str_replace($projectPath, $projectPath . '/app', $yaml);
        $yaml    = str_replace('ip: 192.168.10.10','ip: 10.0.0.33', $yaml);

        file_put_contents('Homestead.yaml', $newYaml);

        chdir($pwd);

        $output->writeln('<info>==== Complete ====</info>');
        $output->writeln("<comment>1. Run the 'cd {$projectName}''</comment>");
        $output->writeln("<comment>2. Run the 'vagrant up'</comment>");
        $output->writeln("<comment>3. Open the http://10.0.0.33</comment>");
    }

}
