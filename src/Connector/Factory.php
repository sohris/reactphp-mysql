<?php


namespace Sohris\Mysql\Connector;

class Factory
{
    /**
     * @var Connector[]
     */
    private static array $connector = [];

    //Create ou Get a Connector class of database connector
    public static function create(
        ?string $user = '',
        ?string $password = '',
        ?string $host = '',
        ?int $port = 3306,
        ?string $database = '',
        ?string $socket = '',
        int $connection_timeout = 5) : Connector
    {
        $hashed = sha1("$user.$password.$host.$port.$database");
        
        if(!isset(self::$connector[$hashed]))
            self::$connector[$hashed] = new Connector(
                $user,
                $password,
                $host,
                $port,
                $database,
                $socket,
                $connection_timeout
            );
        self::$connector[$hashed]->connect();
        return self::$connector[$hashed];
    }

    public static function createLazyConnection(
        ?string $user = '',
        ?string $password = '',
        ?string $host = '',
        ?int $port = 3306,
        ?string $database = '',
        ?string $socket = '',
        int $connection_timeout = 5) : Connector
    {
        $hashed = sha1("$user.$password.$host.$port.$database");
        
        if(!isset(self::$connector[$hashed]))
            self::$connector[$hashed] = new Connector(
                $user,
                $password,
                $host,
                $port,
                $database,
                $socket,
                $connection_timeout
            );
        return self::$connector[$hashed];
    }

    public static function createLazyNewConnector(
        ?string $user = '',
        ?string $password = '',
        ?string $host = '',
        ?int $port = 3306,
        ?string $database = '',
        ?string $socket = '',
        int $connection_timeout = 5) : Connector
    {
        return new Connector(
                $user,
                $password,
                $host,
                $port,
                $database,
                $socket,
                $connection_timeout
            );
    }
}