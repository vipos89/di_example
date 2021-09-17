<?php



include_once __DIR__."/../vendor/autoload.php";


use App\Classes\Task;
use App\DI\Container;


try {
    $container = new Container;
    $obj = $container->get(Task::class);

}catch (Exception $exception){
    echo $exception->getMessage();
}

