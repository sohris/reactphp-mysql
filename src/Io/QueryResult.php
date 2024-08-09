<?php

namespace Sohris\Mysql\Io;


final class QueryResult
{

    /**
     * last inserted ID (if any)
     * @var int|null
     */
    public $insertId;

    /**
     * number of affected rows (for UPDATE, DELETE etc.)
     *
     * @var int|null
     */
    public $affectedRows;
    
    /**
     * result set rows (if any)
     *
     * @var array|null
     */
    public $resultRows;

}