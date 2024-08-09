<?php

namespace Sohris\Mysql\Connector;

use Exception;
use React\Promise\Deferred;
use Sohris\Mysql\Io\Query;
use Sohris\Mysql\Io\QueryResult;

final class Connector extends ConnectorTimer
{

    private ?string $user;
    private ?string $password;
    private ?string $host;
    private ?int $port;
    private ?string $database;
    private ?string $socket;
    private ?int $connection_timeout;


    private Deferred $deferred;
    public bool $running = false;

    protected MysqliConnector $mysqli;
    protected Query $query;


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
        $this->user = $user;
        $this->password = $password;
        $this->host = $host;
        $this->port = $port;
        $this->database = $database;
        $this->socket = $socket;
        $this->connection_timeout = $connection_timeout;
    }

    public function connect()
    {
        if(isset($this->mysqli) && $this->mysqli->ping()) return;
        $this->mysqli = new MysqliConnector($this->user,$this->password,$this->host,$this->port,$this->database,$this->socket,$this->connection_timeout);
        $this->mysqli->id = $this->id;
        mysqli_options($this->mysqli, MYSQLI_OPT_CONNECT_TIMEOUT, $this->connection_timeout);
        $this->mysqli->connect($this->host, $this->user, $this->password, $this->database, $this->port, $this->socket);
    }

    public function ping()
    {
        $this->connect();
        return $this->mysqli->ping();
    }


    public function __destruct()
    {
        $this->mysqli->close();
    }

    public function setOption(int $mysqli_option, $value)
    {
        mysqli_options($this->mysqli, $mysqli_option, $value);
        return $this;
    }

    public function setOptions(array $mysqli_options = [])
    {
        foreach($mysqli_options as $option => $value)
            mysqli_options($this->mysqli, $option, $value);

        return $this;
    }

    public function finish()
    {
        try {
            if (!$result = $this->mysqli->reap_async_query()) {
                $exception = new Exception($this->mysqli->error, $this->mysqli->errno);
                $this->deferred->reject($exception);
            } else {
                $query_result = new QueryResult();
                if ($result === true && $this->mysqli->insert_id) {
                    $query_result->insertId = $this->mysqli->insert_id;
                } else if ($result !== true) {
                    $query_result->affectedRows = $this->mysqli->affected_rows;
                    $query_result->resultRows = $result->fetch_all(MYSQLI_ASSOC);
                }

                $this->deferred->resolve($query_result);
            }
        } catch (Exception $e) {
            $this->deferred->reject($e);
        }
        unset($this->deferred);
        mysqli_next_result($this->mysqli);
        if ($result = mysqli_store_result($this->mysqli))
            mysqli_free_result($result);

        $this->running = false;
    }

    public function query(string $query, ?array $parameters = [])
    {
        if(!isset($this->mysqli))
            $this->connect();
        $this->query = new Query($query, $parameters);
        $this->deferred = new Deferred();

        $this->running = true;
        $this->mysqli->query($this->query->getSQL(), MYSQLI_ASYNC | MYSQLI_STORE_RESULT);
        $this->running();
        return $this->deferred->promise();
    }
}