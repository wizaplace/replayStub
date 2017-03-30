<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace ReplayStub;

trait Recorder
{
    // Everything is static because it must be usable from static methods.
    // But actually, a new anonymous class is created for each mocked object, so these can be used as instance members.

    /**
     * @var array
     */
    private static $records = [];

    /**
     * @var object
     */
    private static $decoratedObject;

    /**
     * @var Registry
     */
    private static $registry;

    /**
     * @var RecorderFactory
     */
    private static $recorderFactory;

    /**
     * @var ?string
     */
    private static $instanceId;

    /**
     * @var ChildrenPolicy
     */
    private static $childrenPolicy;

    /** @noinspection PhpUnusedPrivateMethodInspection */
    private static function ReplayStub_Record(string $name, array $arguments)
    {
        $thrown = null;
        $returned = null;
        try {
            $returned = call_user_func_array([self::$decoratedObject, $name], $arguments);
        } catch (\Exception $e) {
            $thrown = $e;
        }

        if (is_object($returned) && self::$childrenPolicy->shouldBeMocked($returned)) {
            $result = new MockedResult(uniqid((string) self::$instanceId), $returned, $thrown);
        } else {
            $result = new Result($returned, $thrown);
        }

        $call = new Call($name, $arguments, $result, self::$instanceId);

        self::$registry->addCall($call);

        $retVal = $result->produce();

        if ($result instanceof MockedResult) {
            $retVal = self::$recorderFactory->createRecorder($retVal, $result->getInstanceId());
        }

        return $retVal;
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    private function ReplayStub_Init($decoratedObject, Registry $registry, RecorderFactory $recorderFactory, ?string $instanceId, ChildrenPolicy $childrenPolicy)
    {
        self::$decoratedObject = $decoratedObject;
        self::$registry = $registry;
        self::$recorderFactory = $recorderFactory;
        self::$instanceId = $instanceId;
        self::$childrenPolicy = $childrenPolicy;
    }
}
