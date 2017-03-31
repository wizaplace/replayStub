<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace ReplayStub;

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

    public function produce()
    {
        if (!is_null($this->exception)) {
            throw $this->exception;
        }

        return $this->value;
    }

    public function getException() : ?\Exception
    {
        return $this->exception;
    }
}
