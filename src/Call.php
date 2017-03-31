<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace ReplayStub;

final class Call
{
    /**
     * @var string
     */
    private $method;

    /**
     * @var array
     */
    private $args;

    /**
     * @var Result
     */
    private $result;

    /**
     * @var null|string
     */
    private $instanceId;

    public function __construct(string $method, array $args, Result $result, ?string $instanceId = null)
    {
        $this->method = $method;
        $this->args = $args;
        $this->result = $result;
        $this->instanceId = $instanceId;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return array
     */
    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * @return Result
     */
    public function getResult(): Result
    {
        return $this->result;
    }

    /**
     * @return null|string
     */
    public function getInstanceId() : ?string
    {
        return $this->instanceId;
    }
}
