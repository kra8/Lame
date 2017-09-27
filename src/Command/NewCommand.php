<?php

namespace Lame\Command;

use Lame\Command\LameCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class NewCommand extends LameCommand
{
    protected $signature = 'new';

    protected $description = 'Create a new laravel and homestead project.';

    protected function configure()
    {
        parent::configure();

        $this->addArgument('name', InputArgument::REQUIRED, 'Project name');
        $this->addOption('ip', 'i', InputOption::VALUE_REQUIRED, 'Select private ip address', $this->getPrivateIP());
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pwd         = $_SERVER['PWD'];
        $projectName = $input->getArgument('name');
        $projectPath = $pwd . '/' . $projectName;

        if (! $this->setPrivateIP($input->getOption('ip'))) {
            $output->writeln('<error>ERROR: Private ID that can be specified are "10.0.0.0/8", "172.16.0.0/12", and "192.168.0.0/16" .</error>');
            exit(1);
        }

        // if exist file
        if (file_exists($projectPath)) {
            $output->writeln('<error>ERROR:' . $projectName . ' is already exists.</error>');
            exit(1);
        }

        // Create project folder
        mkdir($projectPath);

        $output->writeln('<info>Install Homestead</info>');
        $this->settingHomestead($projectPath);

        $output->writeln('<info>Create a new laravel project to "' . $projectName . '/laravel"</info>');
        $this->newLaravel($projectName . '/laravel');

        $output->writeln('<info>Set up laravel</info>');
        $this->setUpLaravel($projectPath . '/laravel');

        $output->writeln('<info>==== Complete ====</info>');
        $output->writeln("<comment>1. Run the 'cd {$projectName}'</comment>");
        $output->writeln("<comment>2. Run the 'vagrant up'</comment>");
        $output->writeln("<comment>3. Open the http://{$this->getPrivateIP()}</comment>");
    }
}
