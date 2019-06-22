<?php

/**
 * @author   Rafal Przetakowski <rafal.p@beeflow.co.uk>
 * @copyright: (c) 2017 Beeflow Ltd
 *
 * Date: 09.04.17 10:40
 */

namespace Beeflow\SQLQueryManager\Lib;

use Beeflow\SQLQueryManager\Exception\EmptyQueryException;
use Beeflow\SQLQueryManager\Exception\IncorrectValueTypeException;
use Beeflow\SQLQueryManager\Exception\NoQueryException;
use Beeflow\SQLQueryManager\Exception\TypeNotFoundException;
use Beeflow\SQLQueryManager\Lib\Vartypes\VartypeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SQLQueryManager
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
     * @var string
     */
    private $sqlQuery;

    /**
     * Directory where are SQL files
     *
     * @var string
     */
    private $sqlDirectory = 'SQL/';

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $varTypes = [];

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param VartypeInterface $varType
     * @param string           $alias
     *
     * @return SQLQueryManager
     */
    public function addVarType(VartypeInterface $varType, $alias): SQLQueryManager
    {
        $this->varTypes[ $alias ] = $varType;

        return $this;
    }

    /**
     * @param string $alias
     *
     * @return array|NULL
     */
    public function getVarType($alias)
    {
        if (array_key_exists($alias, $this->varTypes)) {
            return $this->varTypes[ $alias ];
        }
    }

    /**
     * @param string $sqlDirectory
     *
     * @return SQLQueryManager
     */
    public function setSqlDirectory($sqlDirectory): SQLQueryManager
    {
        $this->sqlDirectory = $sqlDirectory;

        return $this;
    }

    /**
     *
     * @return string
     */
    public function getQuery(): string
    {
        $sqlQuery = $this->parseConditions();

        if (0 >= count($this->params)) {
            return $sqlQuery;
        }

        foreach ($this->params as $paramName => $paramValue) {
            $paramKey = '{' . $paramName . ((empty($paramValue['type'])) ? '' : ('->' . $paramValue['type'])) . '}';

            if (is_array($paramValue['value'])) {
                $sqlQuery = str_replace($paramKey, implode(',', $paramValue['value']), $sqlQuery);

                continue;
            }

            $sqlQuery = str_replace($paramKey, $paramValue['value'], $sqlQuery);
        }

        return $sqlQuery;
    }

    /**
     * @param string SQL query
     *
     * @return SQLQueryManager
     */
    public function setQuery($query): SQLQueryManager
    {
        $this->sqlQuery = $query;
        $this->explodeQueryParams();

        return $this;
    }

    /**
     * @param string $sqlFileName
     * @param string $sqlDirectory
     *
     * @return SQLQueryManager
     * @throws EmptyQueryException
     * @throws NoQueryException
     */
    public function openFile($sqlFileName, $sqlDirectory = null): SQLQueryManager
    {
        $fileToOpen = (empty($sqlDirectory) ? $this->sqlDirectory : $sqlDirectory) . $sqlFileName . ".sql";

        if (!file_exists($fileToOpen)) {
            throw new NoQueryException('There is no such query as ' . $this->sqlDirectory . $sqlFileName);
        }

        $handle = fopen($fileToOpen, "r");

        if (0 !== filesize($fileToOpen)) {
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
     *
     * @throws EmptyQueryException
     * @throws NoQueryException
     * @throws \Exception
     */
    public function __call($name, $arguments): void
    {
        if (isset($arguments[1])) {
            $sqlDir = $arguments[1];
            if (substr($arguments[1], -1) != '/') {
                $sqlDir .= '/';
            }
            $this->openFile($name, $sqlDir);
        } else {
            $this->openFile($name);
        }

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
     * @throws \Exception
     */
    public function __set($parameterName, $parameterValue): void
    {
        if (!isset($this->params[ $parameterName ])) {
            return;
        }

        if (is_array($parameterValue)) {
            $this->params[ $parameterName ]['value'] = $this->getImplodeArrayValue($parameterValue);

            return;
        }

        if (!is_object($parameterValue) && !$this->isEmptyParamType($parameterName)) {
            $parameterValue = $this->getObjectOfParameterValue($parameterName, $parameterValue);
            $this->params[ $parameterName ]['value'] = $parameterValue->val();

            return;
        }

        $this->params[ $parameterName ]['value'] = is_numeric($parameterValue) ? $parameterValue : "'" . $parameterValue . "'";
    }

    /**
     *
     * @param array $arrayValue
     *
     * @return string
     */
    private function getImplodeArrayValue(array $arrayValue): string
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
     * @return string|null
     */
    private function getParamType($parameterName): ?string
    {
        return $this->params[ $parameterName ]['type'];
    }

    private function explodeQueryParams(): void
    {
        $matches = array();
        $this->params = array();

        preg_match_all(
            '/{(?!CON)(.*?)(?!CON)}/',
            str_replace(array("\n", "\r"), " ", $this->sqlQuery),
            $matches, PREG_PATTERN_ORDER
        );

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
    private function isEmptyParamType($paramName): bool
    {
        $paramType = $this->getParamType($paramName);

        return empty($paramType) ? true : false;
    }

    /**
     *
     * @param string $parameterName
     * @param mixed  $parameterValue
     *
     * @return VartypeInterface
     * @throws \Exception
     */
    private function getObjectOfParameterValue($parameterName, $parameterValue): VartypeInterface
    {
        try {
            $paramType = $this->getParamType($parameterName);

            if (empty($paramType)) {
                throw new IncorrectValueTypeException("The parameter $parameterName is the wrong data type...");
            }

            $paramClass = $this->getVarType($paramType);

            if (!($paramClass instanceof VartypeInterface)) {
                throw new TypeNotFoundException('There is no such type as ' . $paramType);
            }

            $parameterValue = $paramClass->setValue($parameterValue);
        } catch (\Exception $e) {
            throw new \Exception("$parameterName field error: " . $e->getMessage());
        }

        return $parameterValue;
    }

    /**
     *
     * @return string SQLQuery
     */
    private function parseConditions(): string
    {
        $sqlQuery = $this->sqlQuery;
        $matches = array();
        $a = 0;
        $rec = array();
        preg_match_all(
            '{CON\[(.*?)\]:(.*?):CON}',
            str_replace(array("\n", "\r"), " ", $sqlQuery),
            $matches,
            PREG_PATTERN_ORDER
        );
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
                    $sqlQuery = str_replace(
                        "{CON[{$parameterName}]:" . $arrayValue . ":CON}",
                        $arrayValue,
                        str_replace(["\n", "\r", "\t"], " ", $sqlQuery)
                    );
                }
                continue;
            }

            $sqlQuery = str_replace(
                "{CON[{$parameterName}]:" . $rec[ $parameterName ] . ":CON}",
                $rec[ $parameterName ],
                str_replace(array("\n", "\r", "\t"), " ", $sqlQuery)
            );
        }

        return $this->clearUnsetConditions($sqlQuery, $keys, $records);
    }

    /**
     * @param string $sqlQuery
     * @param array  $keys
     * @param array  $records
     *
     * @return string
     */
    private function clearUnsetConditions($sqlQuery, $keys, $records): string
    {
        $a = 0;

        foreach ($keys as $key => $value) {
            $sqlQuery = str_replace(
                "{CON[{$value}]:" . $records[ $a ] . ":CON}",
                "",
                str_replace(["\n", "\r", "\t"], " ", $sqlQuery)
            );
            $a++;
        }

        return $sqlQuery;
    }
}
