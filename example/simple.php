<?php


require "../vendor/autoload.php";

$user = "user";
$password = "pass";
$host = "host";
$port = 3306;

//Create a connection
//The connection is established now
$connector = Sohris\Mysql\Connector\Factory::create($user, $password, $host, $port);
 

$connector->query("SELECT * FROM information_schema.ROUTINES Limit 1")
            ->then(function(Sohris\Mysql\Io\QueryResult $result){   
                        var_dump($result->resultRows);
                    },
                    function(Exception $e){
                        var_dump($e->getMessage());
                    });