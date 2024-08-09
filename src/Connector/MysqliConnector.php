<?php

namespace Sohris\Mysql\Connector;

class MysqliConnector extends \mysqli
{
    public int $id;

    public function __construct(
        ?string $user,
        ?string $password,
        ?string $host,
        ?int $port,
        ?string $database,
        ?string $socket,
        int $connection_timeout = 5
    ) {
        parent::__construct();
        mysqli_options($this, MYSQLI_OPT_CONNECT_TIMEOUT, $connection_timeout);
        $this->connect($host, $user, $password, $database, $port, $socket);
        
    }
}