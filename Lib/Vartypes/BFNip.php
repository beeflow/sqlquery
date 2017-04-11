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

use Beeflow\SQLQueryManager\Exception\IncorrectValueTypeException;

/**
 * nip - polish vat no
 * if you want to check european vat no, see:
 * http://www.phpclasses.org/package/2280-PHP-Check-if-a-European-VAT-number-is-valid.html
 *
 * @author Rafal Przetakowski <rafal.p@beeflow.co.uk>
 */
class BFNip implements VartypeInterface
{

    /**
     *
     * @var Mixed
     */
    private $value;

    /**
     *
     * @param Mixed $val
     *
     * @throws \Exception
     */
    public function __construct($value = null)
    {
        if (!empty($value)) {
            $this->setValue($value);
        }
    }

    /**
     *
     * @return string
     */
    public function val()
    {
        return $this->__toString();
    }

    /**
     *
     * @return String
     */
    public function __toString()
    {
        return (string)$this->value;
    }

    /**
     * @param $value
     *
     * @return $this
     * @throws IncorrectValueTypeException
     */
    public function setValue($value)
    {
        $value = str_replace('-', '', $value);
        if (strlen($value) <> 10) {
            throw new IncorrectValueTypeException('Value must be correct ' . __CLASS__ . ' type');
        }

        $wagi = '657234567';
        $suma = 0;

        for ($i = 0; $i <= 8; $i++) {
            $suma += (integer)$value[ $i ] * (integer)$wagi[ $i ];
        }

        if ((integer)$value[9] == ($suma % 11)) {
            $this->value = $value;
        } else {
            throw new IncorrectValueTypeException('Value must be correct ' . __CLASS__ . ' type');
        }

        return $this;
    }
}
