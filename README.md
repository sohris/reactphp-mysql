
ReactPHP MySQL is a package that provides an asynchronous connection to MySQL databases, enabling non-blocking execution for high-performance operations. Utilizing an event-driven model, it allows the creation of a connection pool, where multiple connection threads to the database are established. This enables parallel execution of scripts and queries, optimizing processing and operational efficiency.

## Features

- **Asynchronous Connections**: Non-blocking MySQL connections for enhanced performance.
- **Pool of Parallel Queries**: Execute multiple queries simultaneously to improve efficiency.

## Installation



## Usage

```php
require 'vendor/autoload.php';

$user = "user";
$password = "pass";
$host = "host";
$port = 3306;

$connector = Sohris\Mysql\Connector\Factory::createLazyConnection($user, $password, $host, $port);


$connector->query("SELECT * FROM information_schema.ROUTINES Limit 1")
            ->then(function(Sohris\Mysql\Io\QueryResult $result){   
                        var_dump($result->resultRows);
                    },
                    function(Exception $e){
                        var_dump($e->getMessage());
                    });
```

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.