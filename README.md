# Sohris ReactPHP Mysql 

ReactPHP MySQL is a package that provides an asynchronous connection to MySQL databases, enabling non-blocking execution for high-performance operations. Utilizing an event-driven model, it allows the creation of a connection pool, where multiple connection threads to the database are established. This enables parallel execution of scripts and queries, optimizing processing and operational efficiency.

## Features

- **Asynchronous Connections**: Non-blocking MySQL connections for enhanced performance.
- **Pool of Parallel Queries**: Execute multiple queries simultaneously to improve efficiency.

## Install

Using PHP Composer:

```shell
    compose require sohris/reactphp-mysql
```

## Usage

### Simple usage

Create a single connection with database and execute a query.

```php
require 'vendor/autoload.php';

$user = "user";
$password = "pass";
$host = "host";
$port = 3306;

//Create a new Connection
$connector = Sohris\Mysql\Connector\Factory::create($user, $password, $host, $port);

//Or
//Create a new lazy Connection
$connector = Sohris\Mysql\Connector\Factory::createLazyConnection($user, $password, $host, $port);

//Or
//To force a new connection, use a createLazyNewConnector
$connector = Sohris\Mysql\Connector\Factory::createLazyNewConnector($user, $password, $host, $port);


$connector->query("SELECT * FROM information_schema.ROUTINES Limit 1")
            ->then(function(Sohris\Mysql\Io\QueryResult $result){   
                        var_dump($result->resultRows);
                    },
                    function(Exception $e){
                        var_dump($e->getMessage());
                    });
```

### Pool Usage

The Pool connection provides more connections to the database, allowing for parallel research in the database. Unlike simple usage, to execute a new query, use the exec method in the Pool class.

```php

require __DIR__."/../vendor/autoload.php";

$user = "user";
$password = "pass";
$host = "host";
$port = 3306;


//Create a connection
$connector = new \Sohris\Mysql\Pool($user, $password, $host, $port);

$connector->exec("SELECT * FROM information_schema.ROUTINES Limit 1")
            ->then(function(Sohris\Mysql\Io\QueryResult $result){   
                        echo "Result 1" . PHP_EOL;                        
                    },
                    function(Exception $e){
                        var_dump($e->getMessage());
                    });

```

To configure the maximum number of threads for database connections, use the setPoolSize method in the Pool class to set the limit.

```php

$connector->setPoolSize(2);

```

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.