<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace RePHPlay;

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
     * @var string
     */
    private static $className;

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
    private static function RePHPlay_Record(string $name, array $arguments)
    {
        $thrown = null;
        $returned = null;
        try {
            $returned = call_user_func_array([self::$decoratedObject, $name], $arguments);
        } catch (\Exception $e) {
            $thrown = $e;
        }

        $result = new Result($returned, $thrown);

        $id = new CallId(self::$className, $name, $arguments, self::$instanceId);

        self::$registry->addRecord($id, $result);

        $retVal = $result->getValue();

        if (is_object($retVal) && self::$childrenPolicy->shouldBeMocked($retVal)) {
            static $i = 0;
            $retVal = self::$recorderFactory->createRecorder($retVal, self::$instanceId.' > '.$i);
            $i++;
        }

        return $retVal;
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    private function RePHPlay_Init($decoratedObject, Registry $registry, string $className, RecorderFactory $recorderFactory, ?string $instanceId, ChildrenPolicy $childrenPolicy)
    {
        self::$decoratedObject = $decoratedObject;
        self::$registry = $registry;
        self::$className = $className;
        self::$recorderFactory = $recorderFactory;
        self::$instanceId = $instanceId;
        self::$childrenPolicy = $childrenPolicy;
    }
}
