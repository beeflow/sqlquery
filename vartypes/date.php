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
 * Description of date
 *
 * @author Rafał Przetakowski <rprzetakowski@pr-projektos.pl>
 */
class date extends DateTime {

	private $dateTimeFormat = 'Y-m-d H:i:s';
	private $dateFormat = 'Y-m-d';
	private $timeZone = 'Europe/Warsaw';

	/**
	 * 
	 * @param Mixed $val
	 * @throws Exception
	 */
	public function __construct($time) {
		parent::__construct($time);
	}

	public function val() {
		return $this->__toString();
	}

	public function getDateTime() {
		return $this->__toString();
	}

	public function getDate() {
		return date_format($this, $this->dateFormat);
	}

	/**
	 * 
	 * @param string $dateTimeFormat {new string($dateTimeFormat)}
	 */
	public function setDateTimeFormat(string $dateTimeFormat) {
		$this->dateTimeFormat = $dateTimeFormat->val();
	}

	/**
	 * 
	 * @param string $dateFormat {new string($dateFormat)}
	 */
	public function setDateFormat(string $dateFormat) {
		$this->dateFormat = $dateFormat->val();
	}

	/**
	 * 
	 * @param string $timezone {new string($timezone)}
	 */
	public function setTimezone(string $timezone) {
		parent::setTimezone($timezone->val());
	}

	/**
	 * 
	 * @return Mixed
	 */
	public function __toString() {
		return date_format($this, $this->dateTimeFormat);
	}

}

?>
