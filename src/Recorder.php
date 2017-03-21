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
    private static $records = [];

    private static $decoratedObject;

    private static $registry;

    private static $className;

    private static $recorderFactory;

    private static $instanceId;

    public function __construct($decoratedObject, Registry $registry, string $className, RecorderFactory $recorderFactory, ?string $instanceId = null)
    {
        self::$decoratedObject = $decoratedObject;
        self::$registry = $registry;
        self::$className = $className;
        self::$recorderFactory = $recorderFactory;
        self::$instanceId = $instanceId;
    }

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

        $id = new Id(self::$className, $name, $arguments, self::$instanceId);

        self::$registry->addRecord($id, $result);

        $retVal = $result->getValue();

        if (is_object($retVal)) {
            static $i = 0;
            $retVal = self::$recorderFactory->createRecorder($retVal, self::$instanceId.' > '.$i);
            $i++;
        }

        return $retVal;
    }
}
