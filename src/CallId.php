<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace RePHPlay;

final class CallId
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var string
     */
    private $method;

    /**
     * @var array
     */
    private $args;

    /**
     * @var null|string
     */
    private $instanceId;

    public function __construct(string $class, string $method, array $args, ?string $instanceId = null)
    {
        $this->class = $class;
        $this->method = $method;
        $this->args = $args;
        $this->instanceId = $instanceId;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
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
     * @return null|string
     */
    public function getInstanceId() : ?string
    {
        return $this->instanceId;
    }
}
