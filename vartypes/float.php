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
 * Description of float
 *
 * @author Rafał Przetakowski <rprzetakowski@pr-projektos.pl>
 */
class float {

	/**
	 * 
	 * @var Mixed
	 */
	private $value;
	private $decimals;

	/**
	 * 
	 * @param Mixed $value
	 * @throws Exception
	 */
	public function __construct($value) {
		$value = (float) str_replace(',', '.', $value);

		if (gettype($value) == __CLASS__) {
			$this->value = $value;
		} else {
			throw new Exception('Value must be ' . __CLASS__ . ' type but is ' . gettype($value) . ' - ' . $value);
		}
	}

	public function val() {
		return $this->__toString();
	}

	public function setDecimals($decimals) {
		$this->decimals = $decimals;
	}

	/**
	 * 
	 * @return Mixed
	 */
	public function __toString() {
		if (empty($this->decimals)) {
			return (float) $this->value;
		} else {
			return (float) round($this->value, $this->decimals);
		}
	}

}

?>
