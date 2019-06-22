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
 * @author Rafal Przetakowski <rafal.p@beeflow.co.uk>
 */
class BFDouble implements VartypeInterface
{

    /**
     *
     * @var Mixed
     */
    private $value;

    /**
     * BFDouble constructor.
     *
     * @param null $val
     *
     * @throws IncorrectValueTypeException
     */
    public function __construct($val = null)
    {
        if (!empty($val)) {
            $this->setValue($val);
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
        return (double)$this->value;
    }

    /**
     * @param $val
     *
     * @return VartypeInterface
     * @throws IncorrectValueTypeException
     */
    public function setValue($val): VartypeInterface
    {
        $val = (double)str_replace(',', '.', $val);

        if (gettype($val) == 'double') {
            $this->value = $val;
        } else {
            throw new IncorrectValueTypeException('Value must be ' . __CLASS__ . ' type but is ' . gettype($val) . ' - ' . $val);
        }

        return $this;
    }
}
