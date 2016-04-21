<?php

/**
 * GNU General Public License (Version 2, June 1991)
 *
 * This program is free software; you can redistribute
 * it and/or modify it under the terms of the GNU
 * General Public License as published by the Free
 * Software Foundation; either version 2 of the License,
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will
 * be useful, but WITHOUT ANY WARRANTY; without even the
 * implied warranty of MERCHANTABILITY or FITNESS FOR A
 * PARTICULAR PURPOSE. See the GNU General Public License
 * for more details.
 */

namespace Beeflow\SQLQueryManager\Tests;

use Beeflow\SQLQueryManager\SQLQuery;

/**
 * Class SQLQueryTest
 *
 * @author Rafal Przetakowski <rafal.p@beeflow.co.uk>
 * @package Beeflow\SQLQueryManage\Tests
 */
class SQLQueryTest extends \PHPUnit_Framework_TestCase
{

    private $sqlQuery;

    public function setUp()
    {
        $this->sqlQuery = new SQLQuery();
        $this->sqlQuery->setSqlDirectory('Tests/SQL/');
    }

    /**
     * @expectedException Beeflow\SQLQueryManager\Exception\NoQueryException
     */
    public function testFileNotExists()
    {
        $this->sqlQuery->openFile('unknownSQLQuery');

    }

    /**
     * @expectedException Beeflow\SQLQueryManager\Exception\EmptyQueryException
     */
    public function testEmptyQueryException()
    {
        $this->sqlQuery->openFile('emptyQuery');
    }

    public function testCorrectSQL()
    {
        $expected = "SELECT example1, example2, example3 FROM exampleTable  WHERE example4 = TEST_VALUE AND example5 = 11 AND vatno = 1111111111 and valuearray IN ('one', 'two', 'tree') and valueWithoutParamType = 'value Without Param Type' ";
        $this->sqlQuery->openFile('sqlExample');
        $this->sqlQuery->value = 'TEST_VALUE';

        // if you set a string value it will be set as 0 (zero) because (integer)'ddd' = 0 (zero)
        $this->sqlQuery->value2 = 11;

        // polish vat no algoritm allows to use 1111111111 vat number
        // if you want to check an european vat no see:
        // http://www.phpclasses.org/package/2280-PHP-Check-if-a-European-VAT-number-is-valid.html
        $this->sqlQuery->vatno = '1111111111';

        $this->sqlQuery->valueArrayWithoutAtype = array('one', 'two', 'tree');
        $this->sqlQuery->valueWithoutParamType = "value Without Param Type";

        $actual = $this->sqlQuery->getQuery();

        $this->assertEquals($expected, $actual);
    }

    public function testCorrectSQLWithCondition()
    {
        $expected = "SELECT example1, example2, example3 FROM exampleTable  WHERE example4 = TEST_VALUE AND example5 = 11 AND vatno = 1111111111 and valuearray IN ('one', 'two', 'tree') and valueWithoutParamType = 'value Without Param Type'  and someField = 1 ";
        $this->sqlQuery->openFile('sqlExample');
        $this->sqlQuery->value = 'TEST_VALUE';

        // if you set a string value it will be set as 0 (zero) because (integer)'ddd' = 0 (zero)
        $this->sqlQuery->value2 = 11;
        $this->sqlQuery->vatno = '1111111111';

        $this->sqlQuery->valueArrayWithoutAtype = array('one', 'two', 'tree');
        $this->sqlQuery->valueWithoutParamType = "value Without Param Type";

        // condition !empty()
        $this->sqlQuery->notEmptyValue = 1;

        $actual = $this->sqlQuery->getQuery();

        $this->assertEquals($expected, $actual);

    }

    /**
     * @expectedException \Exception
     */
    public function testIncorrectValue()
    {
        $this->sqlQuery->openFile('sqlExample');
        $this->sqlQuery->value = 'TEST_VALUE';

        // if you set a string value it will be set as 0 (zero) because (integer)'ddd' = 0 (zero)
        $this->sqlQuery->value2 = 11;
        $this->sqlQuery->vatno = '1212111211';
    }
}
