<?php

namespace tests;


use asva\tasker\Database;

class DatabaseTest extends \PHPUnit_Framework_TestCase {

    /** @var  DB */
    protected static $database;

    public function testFunctions()
    {
        $this->assertTrue(null !== Database::$sqls && null !== Database::$db_params,
            "Vital parameters might present");
        $this->assertInstanceOf("PDO", Database::connect(), 'Successfully connects to DB');

    }



}

