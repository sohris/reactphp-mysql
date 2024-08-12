<?php


namespace Sohris\Mysql\Io;

use React\Promise\Deferred;
use React\Promise\PromiseInterface;

final class QueryExecution
{
    public Query $query;
    public Deferred $deferred;

    public function __construct(Query $query, Deferred $deferred)
    {
       $this->query = $query;
       $this->deferred = $deferred;      
       
    }

    public function promise() :PromiseInterface
    {
        return $this->deferred->promise();
    }
}