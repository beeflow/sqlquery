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
 * nip - polish vat no
 * if you want to check european vat no, see:
 * http://www.phpclasses.org/package/2280-PHP-Check-if-a-European-VAT-number-is-valid.html
 * 
 * @author Rafał Przetakowski <rprzetakowski@pr-projektos.pl>
 */
class nip {

	/**
	 * 
	 * @var Mixed
	 */
	private $value;

	/**
	 * 
	 * @param Mixed $val
	 * @throws Exception
	 */
	public function __construct($numer) {

		$numer = str_replace('-', '', $numer);
		if (strlen($numer) <> 10) {
			throw new Exception('Value must be correct ' . __CLASS__ . ' type');
		}

		$wagi = '657234567';
		$suma = 0;

		for ($i = 0; $i <= 8; $i++) {
			$suma += (integer) $numer[$i] * (integer) $wagi[$i];
		}

		if ((integer) $numer[9] == ($suma % 11)) {
			$this->value = $numer;
		} else {
			throw new Exception('Value must be correct ' . __CLASS__ . ' type');
		}
	}

	/**
	 * 
	 * @return string
	 */
	public function val() {
		return $this->__toString();
	}

	/**
	 *
	 * @return String
	 */
	public function __toString() {
		return (string) $this->value;
	}

}

?>
