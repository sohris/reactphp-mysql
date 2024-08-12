<?php

use Sohris\Mysql\Pool;

require __DIR__."/../vendor/autoload.php";

$user = "user";
$password = "pass";
$host = "host";
$port = 3306;


//Create a connection
$connector = new Pool($user, $password, $host, $port);
 

//Set the pool for max 2 threads connection
$connector->setPoolSize(2);


$connector->exec("SELECT * FROM information_schema.ROUTINES Limit 1")
            ->then(function(Sohris\Mysql\Io\QueryResult $result){   
                        echo "Result 1" . PHP_EOL;                        
                    },
                    function(Exception $e){
                        var_dump($e->getMessage());
                    });


$connector->exec("SELECT * FROM information_schema.ROUTINES Limit 1")
        ->then(function(Sohris\Mysql\Io\QueryResult $result){   
                    echo "Result 2" . PHP_EOL;
                },
                function(Exception $e){
                    var_dump($e->getMessage());
                });


$connector->exec("SELECT * FROM information_schema.ROUTINES Limit 1")
->then(function(Sohris\Mysql\Io\QueryResult $result){   
            echo "Result 3" . PHP_EOL;
        },
        function(Exception $e){
            var_dump($e->getMessage());
        });   