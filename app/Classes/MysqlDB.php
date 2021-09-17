<?php


namespace App\Classes;


use App\Interfaces\DBInterface;

class MysqlDB implements DBInterface
{
    public function __construct()
    {
        echo "MYSQL";
    }
}