<?php

namespace Sohris\Mysql;

use InvalidArgumentException;
use Sohris\Mysql\Connector\Connector;
use Sohris\Mysql\Connector\Factory;

final class Pool
{
    private ?string $user;
    private ?string $password;
    private ?string $host;
    private ?string $database;
    private ?int $port;
    private ?string $socket;
    private ?int $connection_timeout = 5;

    private static int $pool_size = 1;

    /**
     * @var Connector[]
     */
    private static array $connections = [];

    public function __construct(
        ?string $user = "",
        ?string $password = "",
        ?string $host = "",
        ?int $port = 3306,
        ?string $database = null,
        ?string $socket = null,
        ?int $connection_timeout = 5
    ) {
        $user | $this->user = $user;
        $password | $this->password = $password;
        $host | $this->host = $host;
        $port | $this->port = $port;
        $database | $this->database = $database;
        $socket | $this->socket = $socket;
        $connection_timeout | $this->connection_timeout = $connection_timeout;
    }

    /**
     * 
     * @throws InvalidArgumentException The size can not be lass than 1
     */
    public function setPoolSize(int $pool_size)
    {
        if($pool_size <= 0) 
            throw new InvalidArgumentException("The size can not be less than 1");

        self::$pool_size = $pool_size;
    }

    public function exec(string $query, array $parameters = [])
    {        
        $conn = $this->getConnection();
        return $conn->query($query, $parameters);
    }
    
    private function getConnection() : Connector
    {
        if(sizeof(self::$connections) <= 0)
            return $this->createNewConnection();

        //Get the connector with least query in the queue
        $connection = array_reduce(self::$connections, fn(?Connector $a, Connector $current) => !is_null($a) ? ($current->size() <  $a->size()? $current: $a) : $current);

        //If the pool is not over, create new connector to usage
        if($connection->size() > 0 && sizeof(self::$connections) < self::$pool_size)
        {
            $connection = $this->createNewConnection();
        }
        return $connection;        

    }

    //Create a new connection and add in the pool
    private function createNewConnection() : Connector
    {        
        $connector = Factory::createLazyNewConnector($this->user, $this->password, $this->host, $this->port, $this->database, $this->socket,$this->connection_timeout);
        self::$connections[] =  $connector;
        return $connector;
    }
}