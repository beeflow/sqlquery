<?php
/**
 * @author   Rafal Przetakowski <rafal.p@beeflow.co.uk>
 * @copyright: (c) 2017 Beeflow Ltd
 *
 * Date: 09.04.17 10:22
 */

namespace Beeflow\SQLQueryManager\Lib\Vartypes;

use Beeflow\SQLQueryManager\Exception\IncorrectValueTypeException;

interface VartypeInterface
{
    /**
     * VartypeInterface constructor.
     *
     * @param $value
     */
    public function __construct($value = null);

    /**
     * @param $value
     *
     * @return VartypeInterface
     * @throws IncorrectValueTypeException
     */
    public function setValue($value): VartypeInterface;

    /**
     * Returns value in correct type
     *
     * @return mixed
     */
    public function val();

    /**
     * @return mixed
     */
    public function __toString();
}
