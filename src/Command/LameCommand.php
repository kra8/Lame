<?php

namespace Lame\Command;

use Symfony\Component\Console\Command\Command;

class LameCommand extends Command
{
    protected $signature;

    protected $description;

    private $privateIP = '10.0.0.33';

    protected function configure()
    {
        $this->setName($this->signature);
        $this->setDescription($this->description);
    }

    /**
     * Setting Homestead file.
     *
     * @param String $path
     */
    protected function settingHomestead(String $path)
    {
        $pwd = $_SERVER['PWD'];

        chdir($path);

        $execute = 'composer require laravel/homestead --dev';
        shell_exec($execute);

        $execute = 'php vendor/bin/homestead make';
        shell_exec($execute);

        // buckup setting file
        $execute = 'cp Homestead.yaml Homestead.yaml.org';
        shell_exec($execute);

        // setting homestead
        $yaml    = file_get_contents('Homestead.yaml');
        $yaml    = str_replace($path, $path . '/laravel', $yaml);
        $yaml    = str_replace('ip: 192.168.10.10','ip: ' . $this->privateIP, $yaml);

        file_put_contents('Homestead.yaml', $yaml);

        chdir($pwd);
    }

    /**
     * Create a new laravel project.
     *
     * @param String $path
     */
    protected function newLaravel(String $path)
    {
        $execute = $_SERVER['HOME'] . '/.composer/vendor/bin/laravel new ' . $path;
        shell_exec($execute);
    }

    /**
     * Set up laravel.
     *
     * @param String $path
     */
    protected function setUpLaravel(String $path)
    {
        $pwd = $_SERVER['PWD'];
        chdir($path);

        $execute = 'cp -p .env.example .env';
        shell_exec($execute);

        $execute = 'php artisan key:generate';
        shell_exec($execute);

        chdir($pwd);
    }

    protected function getPrivateIP()
    {
        return $this->privateIP;
    }

    protected function setPrivateIP(String $ip)
    {
        if (! $this->validatePrivateIP($ip)) {
            return false;
        }

        $this->privateIP = $ip;
        return true;
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
