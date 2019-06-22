<?php

/**
 * @copyright 2019 Beeflow Ltd
 * @author    Rafal Przetakowski <rafal.p@beeflow.co.uk>
 */

namespace Beeflow\SQLQueryManager\Lib\Query;

use Beeflow\SQLQueryManager\Lib\Vartypes\BFNip;
use Beeflow\SQLQueryManager\Lib\Vartypes\BFSecureString;

class SqlExample
{
    /**
     * @var BFSecureString
     */
    private $value;

    /**
     * @var int
     */
    private $value2;

    /**
     * @var BFNip
     */
    private $vatno;

    /**
     * @var array
     */
    private $valueArrayWithoutAtype;

    /**
     * @var mixed
     */
    private $valueWithoutParamType;

    /**
     * @var int|null
     */
    private $notEmptyValue;

    /**
     * @param BFSecureString $value
     *
     * @return SqlExample
     */
    public function setValue(BFSecureString $value): SqlExample
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @param int $value2
     *
     * @return SqlExample
     */
    public function setValue2(int $value2): SqlExample
    {
        $this->value2 = $value2;

        return $this;
    }

    /**
     * @param BFNip $vatno
     *
     * @return SqlExample
     */
    public function setVatno(BFNip $vatno): SqlExample
    {
        $this->vatno = $vatno;

        return $this;
    }

    /**
     * @param array $valueArrayWithoutAtype
     *
     * @return SqlExample
     */
    public function setValueArrayWithoutAtype(array $valueArrayWithoutAtype): SqlExample
    {
        $this->valueArrayWithoutAtype = $valueArrayWithoutAtype;

        return $this;
    }

    /**
     * @param mixed $valueWithoutParamType
     *
     * @return SqlExample
     */
    public function setValueWithoutParamType($valueWithoutParamType): SqlExample
    {
        $this->valueWithoutParamType = $valueWithoutParamType;

        return $this;
    }

    /**
     * @param int|null $notEmptyValue
     *
     * @return SqlExample
     */
    public function setNotEmptyValue(?int $notEmptyValue): SqlExample
    {
        $this->notEmptyValue = $notEmptyValue;

        return $this;
    }
}