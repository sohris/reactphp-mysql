<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Sohris\Mysql\Io\Query;

class QueryTest extends TestCase
{
    public function testBuildQuery(): void
    {
        $query = new Query("SELECT 1;");
        
        $this->assertEquals("SELECT 1;",$query->getSQL());
    }

    public function testBuildQueryParametersInteger(): void
    {
        $query = new Query("SELECT 1 WHERE a = ?;", [123]);
        
        $this->assertEquals("SELECT 1 WHERE a = 123;", $query->getSQL());
    }

    public function testBuildQueryParametersString(): void
    {
        $query = new Query("SELECT 1 WHERE a = ?;", ["abc"]);
        
        $this->assertEquals("SELECT 1 WHERE a = 'abc';", $query->getSQL());
    }

    public function testBuildQueryParametersNull(): void
    {
        $query = new Query("SELECT 1 WHERE a = ?;", [NULL]);
        
        $this->assertEquals("SELECT 1 WHERE a = NULL;", $query->getSQL());
    }

}