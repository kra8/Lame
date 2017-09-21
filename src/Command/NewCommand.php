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

    private $defaultPrivateIP = '10.0.0.33';

    protected function configure()
    {
        $this->setName($this->signature);
        $this->setDescription('Create a new laravel and homestead project.');
        $this->addArgument('name', InputArgument::REQUIRED, 'Project name');
        // $this->addOption('target', 't', InputOption::VALUE_REQUIRED, 'Select install laravel application version');
        $this->addOption('ip', 'i', InputOption::VALUE_REQUIRED, 'Select private ip address', $this->defaultPrivateIP);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pwd         = $_SERVER['PWD'];
        $projectName = $input->getArgument('name');
        $projectPath = $pwd . '/' . $projectName;
        $privateIP   = $input->getOption('ip');

        // validate option argument
        if (! $this->validatePrivateIP($privateIP)) {
            $output->writeln('<error>ERROR: Private ID that can be specified are "10.0.0.0/8", "172.16.0.0/12", and "192.168.0.0/16" .</error>');
            exit(1);
        }

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
        $execute = $_SERVER['HOME'] . '/.composer/vendor/bin/laravel new ' . $projectName . '/laravel';
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
        $yaml    = str_replace($projectPath, $projectPath . '/laravel', $yaml);
        $yaml    = str_replace('ip: 192.168.10.10','ip: ' . $privateIP, $yaml);

        file_put_contents('Homestead.yaml', $yaml);

        // generate env file
        chdir($projectPath . '/laravel');

        $execute = 'cp -p .env.example .env';
        shell_exec($execute);

        $execute = 'php artisan key:generate';
        shell_exec($execute);

        // return pwd
        chdir($pwd);

        $output->writeln('<info>==== Complete ====</info>');
        $output->writeln("<comment>1. Run the 'cd {$projectName}'</comment>");
        $output->writeln("<comment>2. Run the 'vagrant up'</comment>");
        $output->writeln("<comment>3. Open the http://{$privateIP}</comment>");
    }

    protected function validatePrivateIP(String $ip)
    {
        $classA = '/^10\.(([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){2}([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/';
        $classB = '/^172\.(1[6-9]|2[0-9]|3[0-1])\.([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/';
        $classC = '/^192\.168\.([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.([1-9]?[0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/';

        $result = (preg_match($classA, $ip) || preg_match($classB, $ip) || preg_match($classC, $ip));

        return $result;
    }

}
