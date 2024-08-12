<?php

namespace Sohris\Mysql\Connector;

use Exception;
use React\EventLoop\Loop;
use React\EventLoop\TimerInterface;
use Sohris\Mysql\Io\Query;

abstract class ConnectorTimer
{
    private static array $connectors = [];
    private static TimerInterface $timer;
    protected int $id;

    protected MysqliConnector $mysqli;
    protected Query $query;

    protected function __construct()
    {        
        $this->id = random_int(11111, 99999);
    }


    /**
     * Stack a connector to verify when query is finished
     */
    protected function running()
    {
        self::$connectors[$this->id] = $this;
        if(isset(self::$timer)) return;        
        self::$timer = Loop::addPeriodicTimer(0.001, fn() => self::check());
    }

    /**
     * Verify a state of a connector stack. When the execution in connectos has ended, this connector finilise the promise inside then.
     */
    private static function check()
    {
        if (empty(self::$connectors)) {
            Loop::cancelTimer(self::$timer);
            return;
        }
        $links = $err = $rej = array_map(fn($el) => $el->mysqli, self::$connectors);
        if (!mysqli_poll($links, $err, $rej, false, 10000))
            return;

        foreach ($links as $key => &$connection) {

            $conn = self::$connectors[$connection->id];
            unset(self::$connectors[$connection->id]);
            
            $conn->finish();
            // TODO Create a dynamic pool size
            // if (self::$min_pool < count(self::$connectors))
            //     unset(self::$connectors[$connection->id]);
        }
    }


}