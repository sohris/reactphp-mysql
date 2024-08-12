<?php


require __DIR__."/../vendor/autoload.php";

$user = "rdanieli";
$password = "mdm239860@";
$host = "192.168.0.11";
$port = 3306;

//Create a connection
//The connection is established now
$connector = Sohris\Mysql\Connector\Factory::create($user, $password, $host, $port);
 

$connector->query("SELECT * FROM information_schema.ROUTINES Limit 1")
            ->then(function(Sohris\Mysql\Io\QueryResult $result) use ($connector){   
                        echo "Result 1" . PHP_EOL;
                        
                        $connector->query("SELECT * FROM information_schema.ROUTINES Limit 1")
                        ->then(function(Sohris\Mysql\Io\QueryResult $result){   
                                    echo "Result 3" . PHP_EOL;
                                },
                                function(Exception $e){
                                    var_dump($e->getMessage());
                                });   
                    },
                    function(Exception $e){
                        var_dump($e->getMessage());
                    });


$connector->query("SELECT * FROM information_schema.ROUTINES Limit 1")
        ->then(function(Sohris\Mysql\Io\QueryResult $result){   
                    echo "Result 2" . PHP_EOL;
                },
                function(Exception $e){
                    var_dump($e->getMessage());
                });