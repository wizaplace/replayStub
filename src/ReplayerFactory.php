<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace ReplayStub;

use Mockery\Expectation;
use Mockery\Generator\CachingGenerator;
use Mockery\Generator\StringManipulationGenerator;
use Mockery\Loader\EvalLoader;
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

    /**
     * @var \Mockery\Container
     */
    private $mockeryContainer;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
        $this->childrenPolicy = new MockAll();
        $this->mockeryContainer = new \Mockery\Container(new CachingGenerator(StringManipulationGenerator::withDefaultPasses()), new EvalLoader());
    }

    public function createReplayer(string $className, /** @noinspection PhpUnusedParameterInspection */
                                   ?string $instanceId = null)
    {
        $mock = $this->mockeryContainer->mock($className);

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

    public function close() : void
    {
        $this->mockeryContainer->mockery_teardown();
        $this->mockeryContainer->mockery_close();
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
