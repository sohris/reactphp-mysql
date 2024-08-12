<?php

namespace Sohris\Mysql\Connector;

use Exception;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;
use Sohris\Mysql\Io\Query;
use Sohris\Mysql\Io\QueryExecution;
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
    private bool $is_running = false;

    /**
     * A queue from queries to execute 
     * @var \SplQueue 
     */
    private \SplQueue $query_queue;

    protected MysqliConnector $mysqli;


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
        $this->query_queue = new \SplQueue;
    }

    public function connect() : void
    {
        if(isset($this->mysqli) && $this->mysqli->ping()) return;
        $this->mysqli = new MysqliConnector($this->user,$this->password,$this->host,$this->port,$this->database,$this->socket,$this->connection_timeout);
        $this->mysqli->id = $this->id;
        mysqli_options($this->mysqli, MYSQLI_OPT_CONNECT_TIMEOUT, $this->connection_timeout);
        $this->mysqli->connect($this->host, $this->user, $this->password, $this->database, $this->port, $this->socket);
    }

    public function ping() : bool
    {
        $this->connect();
        return $this->mysqli->ping();
    }

    public function __destruct() 
    {
        $this->mysqli->close();
    }

    public function setOption(int $mysqli_option, $value) : Connector
    {
        mysqli_options($this->mysqli, $mysqli_option, $value);
        return $this;
    }

    public function setOptions(array $mysqli_options = []) : Connector
    {
        foreach($mysqli_options as $option => $value)
            mysqli_options($this->mysqli, $option, $value);

        return $this;
    }

    public function finish(): void
    {
        $cur = $this->query_queue->current();
        try {
            if (!$result = $this->mysqli->reap_async_query()) {
                $exception = new Exception($this->mysqli->error, $this->mysqli->errno);
                $cur->deferred->reject($exception);
            } else {
                $query_result = new QueryResult();
                if ($result === true && $this->mysqli->insert_id) {
                    $query_result->insertId = $this->mysqli->insert_id;
                } else if ($result !== true) {
                    $query_result->affectedRows = $this->mysqli->affected_rows;
                    $query_result->resultRows = $result->fetch_all(MYSQLI_ASSOC);
                }

                $cur->deferred->resolve($query_result);
            }
        } catch (Exception $e) {
            $cur->deferred->reject($e);
        }

        mysqli_next_result($this->mysqli);
        if ($result = mysqli_store_result($this->mysqli))
            mysqli_free_result($result);

        $this->query_queue->dequeue();
        $this->is_running = false;
        $this->checkQueue();
    }

    public function query(string $query, ?array $parameters = []) : PromiseInterface
    {
        $execution = new QueryExecution(new Query($query, $parameters),new Deferred());
        $this->query_queue->enqueue($execution);

        $this->checkQueue();
        return $execution->promise();
    }

    private function checkQueue() : void
    {
        if($this->query_queue->isEmpty() || $this->is_running){
            return;
        }
            
        $this->is_running = true;
        $this->query_queue->rewind();
        $this->connect();
        $cur = $this->query_queue->current();
        $this->mysqli->query($cur->query->getSQL(), MYSQLI_ASYNC | MYSQLI_STORE_RESULT);
        $this->running();

    }

    public function size() : int
    {
        return $this->query_queue->count();
    }



}