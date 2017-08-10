<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);

namespace ReplayStub;

class MockedResult extends Result
{
    /**
     * @var string
     */
    private $instanceId;

    public function __construct(string $instanceId, $value, ?\Throwable $exception = null)
    {
        $this->instanceId = $instanceId;
        parent::__construct($value, $exception);
    }

    /**
     * @return string
     */
    public function getInstanceId(): string
    {
        return $this->instanceId;
    }

    public function serialize(): string
    {
        return \serialize([
            'serializedParent' => parent::serialize(),
            'instanceId' => $this->instanceId,
        ]);
    }

    public function unserialize($serialized): void
    {
        $data = \unserialize($serialized, [
            'allowed_classes' => false,
        ]);

        $this->instanceId = (string) $data['instanceId'];
        parent::unserialize($data['serializedParent']);
    }
}
