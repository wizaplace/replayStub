<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace RePHPlay;

class Result
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @var \Exception|null
     */
    private $exception;

    public function __construct($value, ?\Exception $exception = null)
    {
        $this->value = $value;
        $this->exception = $exception;
    }

    public function getValue()
    {
        if (!is_null($this->exception)) {
            throw $this->exception;
        }

        return $this->value;
    }
}
