<?php

namespace Lame\Command;

use Lame\Command\LameCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AdaptCommand extends LameCommand
{
    protected $signature = 'adapt';

    protected $description = 'Adapt Homestead to existing Laravel project';

    protected function configure()
    {
        parent::configure();

        $this->addArgument('name', InputArgument::REQUIRED, 'Project name');
        $this->addOption('ip', 'i', InputOption::VALUE_REQUIRED, 'Select private ip address', $this->getPrivateIP());
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pwd         = $_SERVER['PWD'];
        $projectName = rtrim($input->getArgument('name'), '/');
        $projectPath = $pwd . '/' . $projectName;

        // validate option argument
        if (! $this->setPrivateIP($input->getOption('ip'))) {
            $output->writeln('<error>ERROR: Private ID that can be specified are "10.0.0.0/8", "172.16.0.0/12", and "192.168.0.0/16" .</error>');
            exit(1);
        }

        // if not exist file
        if (! file_exists($projectPath)) {
            $output->writeln('<error>ERROR: Not found ' . $projectName . '.</error>');
            exit(1);
        }

        $tmpName = uniqid($projectName);
        $execute = "mv {$projectName} {$tmpName}";
        shell_exec($execute);

        mkdir($projectName);

        $execute = "mv {$tmpName} {$projectName}/laravel";
        shell_exec($execute);

        $output->writeln('<info>Install Homestead</info>');
        $this->settingHomestead($projectPath);

        $output->writeln('<info>Set up laravel</info>');
        $this->setUpLaravel($projectPath . '/laravel');

        $output->writeln('<info>==== Complete ====</info>');
        $output->writeln("<comment>1. Run the 'cd {$projectName}'</comment>");
        $output->writeln("<comment>2. Run the 'vagrant up'</comment>");
        $output->writeln("<comment>3. Open the http://{$this->getPrivateIP()}</comment>");
    }

}
