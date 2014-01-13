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
 * Description of double
 *
 * @author Rafał Przetakowski <rprzetakowski@pr-projektos.pl>
 */
class double {

	/**
	 * 
	 * @var Mixed
	 */
	private $value;

	/**
	 * 
	 * @param Mixed $val
	 * @param $lenght ilość miejsc po przecinku
	 * @throws Exception
	 */
	public function __construct($val, $lenght = null) {
		$val = (double) str_replace(',', '.', $val);

		if (gettype($val) == __CLASS__) {
			if (empty($lenght)) {
				$this->value = $val;
			} else {
				$this->value = (double) number_format($val, $lenght, ".", '');
			}
		} else {
			throw new Exception('Value must be ' . __CLASS__ . ' type but is ' . gettype($val) . ' - ' . $val);
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
		return (double) $this->value;
	}

}

?>
