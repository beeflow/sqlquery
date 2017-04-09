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
class BFSecureString implements VartypeInterface
{

    /**
     * Wartość zmienej
     * @var Mixed
     */
    private $value;

    /**
     *
     * @param Mixed $value
     *
     * @throws Exception
     */
    public function __construct($value = null)
    {
        if (!empty($value)) {
            $this->setValue($value);
        }
    }

    public function val()
    {
        return $this->__toString();
    }

    /**
     *
     * @return Mixed
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
        $value = strtr(addslashes(strip_tags($value)), array(
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
        ));

        if (gettype($value) == 'string') {
            $this->value = $value;
        } else {
            throw new \Exception('Value must be ' . __CLASS__ . ' type but is ' . gettype($value));
        }

        return $this;
    }
}
