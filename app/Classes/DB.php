<?php


namespace App\Classes;


use App\Interfaces\DBInterface;

class DB implements DBInterface
{
    public function __construct()
    {
        echo "DB created";
    }
}