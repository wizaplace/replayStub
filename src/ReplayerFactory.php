<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace ReplayStub;

use Mockery\Expectation;
use Mockery\Mock;
use ReplayStub\ChildrenPolicy\MockAll;

class ReplayerFactory
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var ChildrenPolicy
     */
    private $childrenPolicy;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
        $this->childrenPolicy = new MockAll();
    }

    public function createReplayer(string $className, /** @noinspection PhpUnusedParameterInspection */
                                   ?string $instanceId = null)
    {
        $mock = \Mockery::mock($className);
        /** @var Mock $mock */

        $calls = $this->registry->getData();
        foreach ($calls as $call) {
            if ($call->getInstanceId() != $instanceId) {
                continue;
            }

            $mockedCall = call_user_func_array([$mock->expects(), $call->getMethod()], $call->getArgs());
            /** @var Expectation $mockedCall */
            $mockedCall->once();
            $mockedCall->ordered($instanceId);

            $result = $call->getResult();
            $exception = $result->getException();
            if (is_null($exception)) {
                $retVal = $result->produce();
                if ($result instanceof MockedResult) {
                    $retVal = $this->createReplayer(get_class($retVal), $result->getInstanceId());
                }

                $mockedCall->andReturn($retVal);
            } else {
                $mockedCall->andThrow($exception);
            }
        }
        return $mock;
    }

    public function getChildrenPolicy(): ChildrenPolicy
    {
        return $this->childrenPolicy;
    }

    public function setChildrenPolicy(ChildrenPolicy $childrenPolicy)
    {
        $this->childrenPolicy = $childrenPolicy;
    }
}
