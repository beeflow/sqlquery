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

/**
 * A simple SQL query manager, with option to secure queries by setting parameter type. 
 * It uses classes which represents siple var types as string, integer etc... and own classes 
 * like secureString, email etc...
 *
 * To better secure queries, you can create your own var types classes, for example password or phone
 * 
 * @example SELECT example1 FROM exampleTable WHERE example = {value->secureString}
 * 
 * @author Rafał Przetakowski <rprzetakowski@pr-projektos.pl>
 */
class sqlQuery {

	/**
	 * SQL params
	 * @var array 
	 */
	private $params = array();

	/**
	 * SQL query
	 * @var type 
	 */
	private $sqlQuery;

	/**
	 * Directory where are SQL files
	 * @var string 
	 */
	private $sqlDirectory = 'SQL/';

	/**
	 * 
	 * @param string $sqlFileName
	 * @return boolean
	 */
	public function __construct($sqlFileName = NULL) {
		if (!empty($sqlFileName)) {
			if (!$this->openFile($sqlFileName)) {
				return false;
			}
		}
	}

	public function setSqlDirectory($sqlDirectory) {
		$this->sqlDirectory = sqlDirectory;
	}

	/**
	 * 
	 * @return string
	 */
	public function getQuery() {
		$_sqlQuery = $this->sqlQuery;
		if (0 < count($this->params)) {
			foreach ($this->params as $paramName => $paramValue) {
				$paramKey = '{' . $paramName . ( ( empty($paramValue['type']) ) ? '' : ('->' . $paramValue['type'])) . '}';
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
	 */
	public function setQuery($query) {
		$this->sqlQuery = $query;
		$this->explodeQueryParams();
	}

	/**
	 *
	 * @param string $sqlFileName
	 * @return boolean
	 */
	public function openFile($sqlFileName) {
		$handle = fopen($this->sqlDirectory . $sqlFileName . ".sql", "r");
		if (0 != filesize($this->sqlDirectory . $sqlFileName . ".sql")) {
			$this->sqlQuery = fread($handle, filesize($this->sqlDirectory . $sqlFileName . ".sql"));
		} else {
			fclose($handle);
			return false;
		}
		fclose($handle);
		$this->explodeQueryParams();
		return true;
	}

	/**
	 * 
	 * @param string $parameterName
	 * @param Mixed $parameterValue
	 * @return null
	 */
	public function __set($parameterName, $parameterValue) {
		if (!isset($this->params[$parameterName])) {
			return null;
		}

		if (is_array($parameterValue)) {
			$this->params[$parameterName]['value'] = $this->getImplodeArrayValue($parameterValue);
			return;
		}

		if (!is_object($parameterValue) && !$this->isEmptyParamType($parameterName)) {
			$parameterValue = $this->getObjectOfParameterValue($parameterName, $parameterValue);
		} else {
			$this->params[$parameterName]['value'] = is_numeric($parameterValue) ? $parameterValue : "'" . $parameterValue . "'";
			return;
		}

		// list of string vartypes
		if ($parameterValue instanceof string ||
			$parameterValue instanceof email ||
			$parameterValue instanceof date ||
			$parameterValue instanceof secureString ||
			$parameterValue instanceof nip
		) {
			$this->params[$parameterName]['value'] = "'" . $parameterValue->val() . "'";
		} else {
			$this->params[$parameterName]['value'] = $parameterValue->val();
		}
	}

	/**
	 * 
	 * @param array $arrayValue
	 * @return string
	 */
	private function getImplodeArrayValue(array $arrayValue) {
		foreach ($arrayValue as $key => $value) {
			if (!is_numeric($value)) {
				$arrayValue[$key] = "'" . $value . "'";
			}
		}

		return implode(', ', $arrayValue);
	}

	/**
	 * 
	 * @param string $parameterName
	 * @return string
	 */
	private function getParamType($parameterName) {
		return $this->params[$parameterName]['type'];
	}


	/**
	 * 
	 */
	private function explodeQueryParams() {
		$matches = array();
		$this->params = array();

		preg_match_all('/{(.*?)}/', str_replace(array("\n", "\r", "\t", "  "), " ", $this->sqlQuery), $matches, PREG_PATTERN_ORDER);

		$queryKeys = $matches[1];

		foreach ($queryKeys as $value) {
			$paramArray = explode("->", $value);
			$paramName = $paramArray[0];
			$paramType = (isset($paramArray[1]) ? $paramArray[1] : null);

			$this->params[$paramName] = array("value" => null, "type" => $paramType);
		}
	}

	/**
	 * 
	 * @param string $paramName
	 * @return boolean
	 */
	private function isEmptyParamType($paramName) {
		$paramType = $this->getParamType($paramName);
		return empty($paramType) ? true : false;
	}

	/**
	 * 
	 * @param type $parameterName
	 * @param paramType $parameterValue
	 * @return Object
	 * @throws Exception
	 */
	private function getObjectOfParameterValue($parameterName, $parameterValue) {
		try {
			$paramType = $this->getParamType($parameterName);
			if (empty($paramType)) {
				throw new Exception("The parameter $parameterName is the wrong data type...");
			}
			require_once 'vartypes/' . $paramType . '.php';
			$parameterValue = new $paramType($parameterValue);
		} catch (Exception $e) {
			throw new Exception("$parameterName field error: " . $e->getMessage());
		}
		return $parameterValue;
	}

}
