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

namespace Beeflow\SQLQueryManager\Vartypes;

/**
 * @author Rafal Przetakowski <rafal.p@beeflow.co.uk>
 */
class BFString {

	/**
	 * 
	 * @var Mixed
	 */
	private $value;

	/**
	 *
	 * @param Mixed $val
	 * @throws \Exception
	 */
	public function __construct($val, $lenght = null) {
		$lenght = (integer) $lenght;
		if (is_string($val)) {
			if (empty($lenght)) {
				$this->value = $val;
			} else if (0 < $lenght) {
				if (strlen($val) > $lenght) {
					$this->value = substr($val, 0, $lenght - 1);
				}
			}
		} else {
			throw new \Exception('Value must be ' . __CLASS__ . ' type but is ' . gettype($val));
		}
	}

	public function val() {
		return $this->__toString();
	}

	/**
	 * 
	 * @return Mixed
	 */
	public function __toString() {
		return (string) $this->value;
	}

}

