<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace ReplayStub;

require_once(__DIR__.'/functions.php');

final class Call implements \JsonSerializable, \Serializable
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
        $this->init($method, $args, $result, $instanceId);
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

    public function serialize(): string
    {
        return \serialize([
            'method' => $this->getMethod(),
            'args' => $this->getArgs(),
            'result' => $this->getResult(),
            'instanceId' => $this->getInstanceId(),
        ]);
    }

    public function unserialize($serialized): void
    {
        $data = \unserialize($serialized, [
            'allowed_classes' => true,
        ]);

        $this->init($data['method'], $data['args'], $data['result'], $data['instanceId'] ?? null);
    }

    public function jsonSerialize(): array
    {
        return [
            'method' => $this->getMethod(),
            'args' => makeMixedValueJsonEncodable($this->getArgs()),
            'result' => $this->getResult(),
            'instanceId' => $this->getInstanceId(),
        ];
    }

    private function init(string $method, array $args, Result $result, ?string $instanceId = null): void
    {
        $this->method = $method;
        $this->args = $args;
        $this->result = $result;
        $this->instanceId = $instanceId;
    }
}
