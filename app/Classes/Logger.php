<?php


namespace App\Classes;



class Logger
{
    public function __construct(DB $database)
    {
        echo "Logger created\n";
    }
}