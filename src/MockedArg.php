<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types=1);

namespace ReplayStub;

class MockedArg
{
    /**
     * @var ?string
     */
    private $instanceId;

    public function __construct(?string $instanceId)
    {
        $this->instanceId = $instanceId;
    }

    /**
     * @return ?string
     */
    public function getInstanceId(): ?string
    {
        return $this->instanceId;
    }
}
