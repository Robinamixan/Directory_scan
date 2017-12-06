<?php

namespace ScanerDirectory;

class Scaner
{
    public function __construct($directory)
    {
        $this->print_string(0, getcwd());
        $this->scan(0, $directory);
    }

    private function scan(int $level,string $direct): void
    {
        $level++;
        if ($this->check_directory($level, $direct)){
            $curent_contain = scandir($direct);
            if ($curent_contain){
                $this->search_of_directories($level, $direct, $curent_contain);
                $this->print_files_of_directory($level, $direct, $curent_contain);
            }
        }
    }

    private function check_directory(int $level, string $direct):bool
    {
        if (!$this->check_permission($level, $direct)){
            return false;
        } elseif (!$this->check_link($level, $direct)) {
            return false;
        }
        return true;
    }

    private function check_permission(int $level, string $direct):bool
    {
        if (!@opendir($direct)) {
            $this->print_string($level, '<permission denied>');
            return false;
        }
        return true;
    }

    private function check_link(int $level, string $direct):bool
    {
        if (is_link($direct)) {
            $this->print_string($level, '<simlink on ' . readlink($direct) . '>');
            return false;
        }
        return true;
    }

    private function search_of_directories(int $level, string $direct, array $contain): void
    {
        foreach ($contain as $elem){
            if (($elem[0] != '.') && (is_dir($direct . '/' . $elem)))
            {
                $this->print_string($level, $elem);
                $this->scan($level,$direct . '/' . $elem);
            }
        }
    }

    private function print_files_of_directory(int $level, string $direct, array $contain): void
    {
        foreach ($contain as $elem){
            if (($elem[0] != '.') && (!is_dir($direct . '/' . $elem)))
            {
                $this->print_string($level, $elem);
            }
        }
    }

    private function print_string(int $level, string $name): void
    {
        if (php_sapi_name() == 'cli'){
            $this->print_as_cli($level, $name);
        } else {
            $this->print_as_cgi($level, $name);
        }
    }

    private function print_as_cli(int $level, string $name)
    {
        $space = str_repeat('|---', $level);
        echo $space . $name . "\n";
    }

    private function print_as_cgi(int $level, string $name)
    {
        $space = str_repeat('|---', $level);
        $str = $space . $name;
        echo $str . '<br>';
    }
}

new Scaner(__DIR__);