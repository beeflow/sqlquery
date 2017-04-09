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

namespace Beeflow\SQLQueryManager;

use Beeflow\SQLQueryManager\Exception\NoQueryException;
use Beeflow\SQLQueryManager\Exception\EmptyQueryException;

/**
 * A simple SQL query manager, with option to secure queries by setting parameter type.
 * It uses classes which represents siple var types as string, integer etc... and own classes
 * like secureString, email etc...
 *
 * To better secure queries, you can create your own var types classes, for example password or phone
 *
 * @example    SELECT example1 FROM exampleTable WHERE example = {value->secureString}
 *
 * @author     Rafal Przetakowski <rafal.p@beeflow.co.uk>
 * @deprecated Use service SQLQueryManager instead.
 */
class SQLQuery
{

    /**
     * SQL params
     *
     * @var array
     */
    private $params = array();

    /**
     * SQL query
     *
     * @var type
     */
    private $sqlQuery;

    /**
     * Directory where are SQL files
     *
     * @var string
     */
    private $sqlDirectory = 'SQL/';

    /**
     * SQLQuery constructor.
     *
     * @param string|NULL $sqlFileName
     */
    public function __construct($sqlFileName = null)
    {
        if (!empty($sqlFileName)) {
            if (!$this->openFile($sqlFileName)) {
                return false;
            }
        }
    }

    /**
     * @param string $sqlDirectory
     */
    public function setSqlDirectory($sqlDirectory)
    {
        $this->sqlDirectory = $sqlDirectory;
    }

    /**
     *
     * @return string
     */
    public function getQuery()
    {
        $_sqlQuery = $this->parseConditions();
        if (0 < count($this->params)) {
            foreach ($this->params as $paramName => $paramValue) {
                $paramKey = '{' . $paramName . ((empty($paramValue['type'])) ? '' : ('->' . $paramValue['type'])) . '}';
                if (is_array($paramValue['value'])) {
                    $_sqlQuery = str_replace($paramKey, implode(',', $paramValue['value']), $_sqlQuery);
                } else {
                    $_sqlQuery = str_replace($paramKey, $paramValue['value'], $_sqlQuery);
                }
            }
        }

        return $_sqlQuery;
    }

    /**
     * @param string SQL query
     *
     * @return $this
     */
    public function setQuery($query)
    {
        $this->sqlQuery = $query;
        $this->explodeQueryParams();

        return $this;
    }

    /**
     * @param $sqlFileName
     *
     * @return $this
     * @throws EmptyQueryException
     * @throws NoQueryException
     */
    public function openFile($sqlFileName)
    {
        $fileToOpen = $this->sqlDirectory . $sqlFileName . ".sql";

        if (!file_exists($fileToOpen)) {
            throw new NoQueryException('There is no such query as ' . $sqlFileName);
        }

        $handle = fopen($fileToOpen, "r");

        if (0 != filesize($fileToOpen)) {
            $this->sqlQuery = fread($handle, filesize($fileToOpen));
        } else {
            fclose($handle);

            throw new EmptyQueryException('Query ' . $sqlFileName . ' is empty.');
        }

        fclose($handle);
        $this->explodeQueryParams();

        return $this;
    }

    /**
     * @param string $name      name of SQLFile
     * @param array  $arguments list of sql params
     */
    public function __call($name, $arguments)
    {
        $this->openFile($name);

        if (!isset($arguments[0])) {
            return;
        }

        foreach ($arguments[0] as $key => $argument) {
            $this->__set($key, $argument);
        }
    }

    /**
     *
     * @param string $parameterName
     * @param Mixed  $parameterValue
     *
     * @return null
     */
    public function __set($parameterName, $parameterValue)
    {
        if (!isset($this->params[ $parameterName ])) {
            return null;
        }

        if (is_array($parameterValue)) {
            $this->params[ $parameterName ]['value'] = $this->getImplodeArrayValue($parameterValue);

            return;
        }

        if (!is_object($parameterValue) && !$this->isEmptyParamType($parameterName)) {
            $parameterValue = $this->getObjectOfParameterValue($parameterName, $parameterValue);
        } else {
            $this->params[ $parameterName ]['value'] = is_numeric($parameterValue) ? $parameterValue : "'" . $parameterValue . "'";

            return;
        }

        $this->params[ $parameterName ]['value'] = $parameterValue->val();
    }

    /**
     *
     * @param array $arrayValue
     *
     * @return string
     */
    private function getImplodeArrayValue(array $arrayValue)
    {
        foreach ($arrayValue as $key => $value) {
            if (!is_numeric($value)) {
                $arrayValue[ $key ] = "'" . $value . "'";
            }
        }

        return implode(', ', $arrayValue);
    }

    /**
     *
     * @param string $parameterName
     *
     * @return string
     */
    private function getParamType($parameterName)
    {
        return $this->params[ $parameterName ]['type'];
    }

    /**
     *
     */
    private function explodeQueryParams()
    {
        $matches = array();
        $this->params = array();

        preg_match_all('/{(?!CON)(.*?)(?!CON)}/', str_replace(array("\n", "\r"), " ", $this->sqlQuery), $matches, PREG_PATTERN_ORDER);

        $queryKeys = $matches[1];

        foreach ($queryKeys as $value) {
            $paramArray = explode("->", $value);
            $paramName = $paramArray[0];
            $paramType = (isset($paramArray[1]) ? $paramArray[1] : null);

            $this->params[ $paramName ] = array("value" => null, "type" => $paramType);
        }
    }

    /**
     *
     * @param string $paramName
     *
     * @return boolean
     */
    private function isEmptyParamType($paramName)
    {
        $paramType = $this->getParamType($paramName);

        return empty($paramType) ? true : false;
    }

    /**
     *
     * @param type      $parameterName
     * @param paramType $parameterValue
     *
     * @return Object
     * @throws \Exception
     */
    private function getObjectOfParameterValue($parameterName, $parameterValue)
    {
        try {
            $paramType = $this->getParamType($parameterName);
            if (empty($paramType)) {
                throw new \Exception("The parameter $parameterName is the wrong data type...");
            }
            $paramClass = 'Beeflow\SQLQueryManager\Lib\Vartypes\BF' . ucfirst($paramType);
            $parameterValue = new $paramClass($parameterValue);
        } catch (\Exception $e) {
            throw new \Exception("$parameterName field error: " . $e->getMessage());
        }

        return $parameterValue;
    }

    /**
     *
     * @return string SQLQuery
     */
    private function parseConditions()
    {
        $sqlQuery = $this->sqlQuery;
        $matches = array();
        $a = 0;
        $rec = array();
        preg_match_all('{CON\[(.*?)\]:(.*?):CON}', str_replace(array("\n", "\r"), " ", $sqlQuery), $matches, PREG_PATTERN_ORDER);
        $keys = $matches[1];
        $records = $matches[2];

        foreach ($keys as $key => $value) {
            if (isset($rec[ $value ]) && !is_array($rec[ $value ])) {
                $tmp = $rec[ $value ];
                $rec[ $value ] = [$tmp, $records[ $a ]];
            } else if (isset($rec[ $value ]) && is_array($rec[ $value ])) {
                $rec[ $value ][] = $records[ $a ];
            } else {
                $rec[ $value ] = $records[ $a ];
            }

            $a++;
        }

        foreach ($this->params as $parameterName => $value) {
            if (!isset($rec[ $parameterName ]) || empty($value['value'])) {
                continue;
            }

            if (is_array($rec[ $parameterName ])) {
                foreach ($rec[ $parameterName ] as $arrayValue) {
                    $sqlQuery = str_replace("{CON[{$parameterName}]:" . $arrayValue . ":CON}", $arrayValue, str_replace(array("\n", "\r", "\t"), " ", $sqlQuery));
                }
            } else {
                $sqlQuery = str_replace("{CON[{$parameterName}]:" . $rec[ $parameterName ] . ":CON}", $rec[ $parameterName ], str_replace(array("\n", "\r", "\t"), " ", $sqlQuery));
            }
        }

        return $this->clearUnsetConditions($sqlQuery, $keys, $records);
    }

    /**
     * @param string $sqlQuery
     * @param array  $keys
     * @param array  $records
     *
     * @return mixed
     */
    private function clearUnsetConditions($sqlQuery, $keys, $records)
    {
        $a = 0;
        foreach ($keys as $key => $value) {
            $sqlQuery = str_replace("{CON[{$value}]:" . $records[ $a ] . ":CON}", "", str_replace(array("\n", "\r", "\t"), " ", $sqlQuery));
            $a++;
        }

        return $sqlQuery;
    }
}
