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
 * Description of email
 *
 * @author Rafał Przetakowski <rprzetakowski@pr-projektos.pl>
 */
class email {

	/**
	 *
	 * @var Mixed 
	 */
	private $value;

	/**
	 * Regular expression for email address
	 * @var string 
	 */
	private $regexp = "/^[a-z0-9]+([_\\.-][a-z0-9]+)*@([a-z0-9]+([\.-][a-z0-9]+)*)+\\.[a-z]{2,}$/i";

	public function __construct($value) {
		if (!preg_match($this->regexp, $value)) {
			throw new Exception('Value must be correct ' . __CLASS__ . ' type.');
		} else {
			$this->value = $value;
		}
	}

	public function val() {
		return $this->__toString();
	}

	public function __toString() {
		return $this->value;
	}

}

?>
