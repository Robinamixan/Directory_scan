<?php

namespace ScanerDirectory;

class Scaner
{
    public function __construct($directory)
    {
        $this->printString(0, getcwd());
        $this->scan(0, $directory);
    }

    private function scan(int $level,string $direct): void
    {
        $level++;
        if ($this->checkDirectory($level, $direct)){
            $curent_contain = scandir($direct);
            if ($curent_contain){
                $this->searchOfDirectories($level, $direct, $curent_contain);
                $this->printFilesOfDirectory($level, $direct, $curent_contain);
            }
        }
    }

    private function checkDirectory(int $level, string $direct):bool
    {
        return ($this->checkPermission($level, $direct) && $this->checkLink($level, $direct));
    }

    private function checkPermission(int $level, string $direct):bool
    {
        if (!@opendir($direct)) {
            $this->printString($level, '<permission denied>');
            return false;
        }
        return true;
    }

    private function checkLink(int $level, string $direct):bool
    {
        if (is_link($direct)) {
            $this->printString($level, '<simlink on ' . readlink($direct) . '>');
            return false;
        }
        return true;
    }

    private function searchOfDirectories(int $level, string $direct, array $contain): void
    {
        foreach ($contain as $elem){
            if (($elem[0] != '.') && (is_dir($direct . '/' . $elem)))
            {
                $this->printString($level, $elem);
                $this->scan($level,$direct . '/' . $elem);
            }
        }
    }

    private function printFilesOfDirectory(int $level, string $direct, array $contain): void
    {
        foreach ($contain as $elem){
            if (($elem[0] != '.') && (!is_dir($direct . '/' . $elem)))
            {
                $this->printString($level, $elem);
            }
        }
    }

    private function printString(int $level, string $name): void
    {
        if (php_sapi_name() == 'cli'){
            $this->printAsCli($level, $name);
        } else {
            $this->printAsCgi($level, $name);
        }
    }

    private function printAsCli(int $level, string $name)
    {
        $space = str_repeat('|---', $level);
        echo $space, $name, "\n";
    }

    private function printAsCgi(int $level, string $name)
    {
        $space = str_repeat('|---', $level);
        echo $space, $name, '<br>';
    }
}

new Scaner(__DIR__);