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

use Beeflow\SQLQueryManager\Vartype\BFString;

/**
 * @author Rafal Przetakowski <rafal.p@beeflow.co.uk>
 */
class BFDate extends \DateTime
{

    private $dateTimeFormat = 'Y-m-d H:i:s';
    private $dateFormat = 'Y-m-d';
    private $timeZone = 'Europe/Warsaw';

    /**
     * BFDate constructor.
     *
     * @param string $time
     */
    public function __construct($time)
    {
        parent::__construct($time);
    }

    public function val()
    {
        return $this->__toString();
    }

    public function getDateTime()
    {
        return $this->__toString();
    }

    public function getDate()
    {
        return date_format($this, $this->dateFormat);
    }

    /**
     *
     * @param BFString $dateTimeFormat {new BFString($dateTimeFormat)}
     */
    public function setDateTimeFormat($dateTimeFormat)
    {
        $this->dateTimeFormat = $dateTimeFormat->val();
    }

    /**
     *
     * @param BFString $dateFormat {new BFString($dateFormat)}
     */
    public function setDateFormat($dateFormat)
    {
        $this->dateFormat = $dateFormat->val();
    }

    /**
     *
     * @param BFString $timezone {new BFString($timezone)}
     */
    public function setTimezone($timezone)
    {
        parent::setTimezone($timezone->val());
    }

    /**
     *
     * @return Mixed
     */
    public function __toString()
    {
        return date_format($this, $this->dateTimeFormat);
    }

}
