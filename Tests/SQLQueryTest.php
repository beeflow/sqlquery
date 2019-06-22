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

use Beeflow\SQLQueryManager\Exception\EmptyQueryException;
use Beeflow\SQLQueryManager\Exception\NoQueryException;
use Beeflow\SQLQueryManager\Lib\SQLQueryManager;
use Beeflow\SQLQueryManager\Lib\Vartypes\BFInteger;
use Beeflow\SQLQueryManager\Lib\Vartypes\BFNip;
use Beeflow\SQLQueryManager\Lib\Vartypes\BFSecureString;
use Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SQLQueryTest
 *
 * @author  Rafal Przetakowski <rafal.p@beeflow.co.uk>
 * @package Beeflow\SQLQueryManage\Tests
 */
class SQLQueryTest extends TestCase
{
    /**
     * @var SQLQueryManager
     */
    private $sqlQuery;

    /**
     * @throws \ReflectionException
     */
    public function setUp(): void
    {
        $containerInterfaceMock = $this->createMock(ContainerInterface::class);
        $secureStringMock = $this->createMock(BFSecureString::class);
        $secureStringMock->method('setValue')
            ->willReturnReference($secureStringMock);
        $secureStringMock->method('val')
            ->willReturn('TEST_VALUE');

        $integerMock = $this->createMock(BFInteger::class);
        $integerMock->method('setValue')
            ->willReturnReference($integerMock);
        $integerMock->method('val')
            ->willReturn(11);

        $nipMock = $this->createMock(BFNip::class);
        $nipMock->method('setValue')->willReturnReference($nipMock);
        $nipMock->method('val')->willReturn('1111111111');

        $this->sqlQuery = new SQLQueryManager($containerInterfaceMock);
        $this->sqlQuery
            ->addVarType($secureStringMock, 'secureString')
            ->addVarType($integerMock, 'integer')
            ->addVarType($nipMock, 'nip')
            ->setSqlDirectory('Tests/SQL/');
    }

    /**
     * @throws EmptyQueryException
     * @throws NoQueryException
     */
    public function testFileNotExists(): void
    {
        $this->expectException(NoQueryException::class);
        $this->sqlQuery->openFile('unknownSQLQuery');
    }

    /**
     * @throws EmptyQueryException
     * @throws NoQueryException
     */
    public function testEmptyQueryException(): void
    {
        $this->expectException(EmptyQueryException::class);
        $this->sqlQuery->openFile('emptyQuery');
    }

    /**
     * polish vat no algoritm allows to use 1111111111 vat number
     * if you want to check an european vat no see:
     * http://www.phpclasses.org/package/2280-PHP-Check-if-a-European-VAT-number-is-valid.html
     *
     * @throws EmptyQueryException
     * @throws NoQueryException
     */
    public function testCorrectSQL(): void
    {
        $expected = "SELECT example1, example2, example3 FROM exampleTable WHERE example4 = TEST_VALUE AND example5 = 11 AND vatno = 1111111111 and valuearray IN ('one', 'two', 'tree') and valueWithoutParamType = 'value Without Param Type' ";
        $this->sqlQuery->openFile('sqlExample');
        $this->sqlQuery->value = 'TEST_VALUE';

        // if you set a string value it will be set as 0 (zero) because (integer)'ddd' = 0 (zero)
        $this->sqlQuery->value2 = 11;
        $this->sqlQuery->vatno = '1111111111';
        $this->sqlQuery->valueArrayWithoutAtype = array('one', 'two', 'tree');
        $this->sqlQuery->valueWithoutParamType = "value Without Param Type";

        $actual = $this->sqlQuery->getQuery();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @throws EmptyQueryException
     * @throws NoQueryException
     */
    public function testCorrectSQLWithCondition(): void
    {
        $expected = "SELECT example1, example2, example3 FROM exampleTable WHERE example4 = TEST_VALUE AND example5 = 11 AND vatno = 1111111111 and valuearray IN ('one', 'two', 'tree') and valueWithoutParamType = 'value Without Param Type'  and someField = 11 ";
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
     * @throws EmptyQueryException
     * @throws NoQueryException
     * @throws \ReflectionException
     */
    public function testIncorrectValue(): void
    {
        $this->expectException(Exception::class);

        $containerInterfaceMock = $this->createMock(ContainerInterface::class);
        $secureStringMock = $this->createMock(BFSecureString::class);
        $secureStringMock->method('setValue')
            ->willReturnReference($secureStringMock);
        $secureStringMock->method('val')
            ->willReturn('TEST_VALUE');

        $integerMock = $this->createMock(BFInteger::class);
        $integerMock->method('setValue')
            ->willReturnReference($integerMock);
        $integerMock->method('val')
            ->willReturn(11);

        $nipMock = $this->createMock(BFNip::class);
        $nipMock->method('setValue')->willReturnReference($nipMock);
        $nipMock->method('val')->willThrowException(new Exception());

        $sqlQuery = new SQLQueryManager($containerInterfaceMock);
        $sqlQuery
            ->addVarType($secureStringMock, 'secureString')
            ->addVarType($integerMock, 'integer')
            ->addVarType($nipMock, 'nip')
            ->setSqlDirectory('Tests/SQL/');

        $sqlQuery->openFile('sqlExample');
        $sqlQuery->value = 'TEST_VALUE';

        // if you set a string value it will be set as 0 (zero) because (integer)'ddd' = 0 (zero)
        $sqlQuery->value2 = 11;
        $sqlQuery->vatno = '1212111211';
    }
}
