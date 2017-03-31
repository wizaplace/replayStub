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

    public function __construct(string $instanceId, $value, ?\Exception $exception = null)
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
}
