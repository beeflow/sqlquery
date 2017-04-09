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

namespace Beeflow\SQLQueryManager\Lib\Vartypes;

/**
 * @author Rafal Przetakowski <rafal.p@beeflow.co.uk>
 */
class BFDate implements VartypeInterface
{

    private $dateTimeFormat = 'Y-m-d H:i:s';
    private $dateFormat = 'Y-m-d';
    private $timeZone = 'Europe/Warsaw';

    /**
     * @var \DateTime
     */
    private $date;

    /**
     * BFDate constructor.
     *
     * @param string $time
     */
    public function __construct($time = null)
    {
        if (!empty($time)) {
            $this->setValue($time);
        }
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
        return date_format($this->date, $this->dateFormat);
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
     * @param \DateTimeZone $timezone
     *
     * @return \DateTime
     */
    public function setTimezone(\DateTimeZone $timezone)
    {
        $this->date->setTimezone($timezone->val());

        return $this->date;
    }

    /**
     *
     * @return Mixed
     */
    public function __toString()
    {
        return date_format($this->date, $this->dateTimeFormat);
    }

    /**
     * @param $value
     *
     * @throws \Exception
     */
    public function setValue($value)
    {
        $this->date = new \DateTime($value);

        return $this;
    }
}
