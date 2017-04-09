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
 * Class BFPsqlSecureString
 *
 * @author  Rafal Przetakowski <rafal.p@beeflow.co.uk>
 * @package Beeflow\SQLQueryManager\Vartypes
 */
class BFPsqlSecureString implements VartypeInterface
{
    /**
     * Wartość zmienej
     *
     * @var Mixed
     */
    private $value;

    /**
     * BFPsqlSecureString constructor.
     *
     * @param $value
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
     * @return String
     */
    public function val()
    {
        return $this->__toString();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->value;
    }

    /**
     * @param $value
     *
     * @throws \Exception
     */
    public function setValue($value)
    {
        $value = strtr(
            strip_tags($value),
            array(
                "'"    => "''",
                "\0"   => "",
                "--"   => "",
                ");"   => "",
                ")"    => "",
                "}"    => "",
                "("    => "",
                "("    => "",
                "<!--" => "",
                "<"    => "&lt;",
                ">"    => "&gt;"
                // more secure
            )
        );

        if (gettype($value) == 'string') {
            $this->value = $value;
        } else {
            throw new \Exception('Value must be ' . __CLASS__ . ' type but is ' . gettype($value));
        }
    }
}
