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
    private static $records = [];

    private static $decoratedObject;

    private static $registry;

    private static $className;

    public function __construct($decoratedObject, Registry $registry, string $className)
    {
        self::$decoratedObject = $decoratedObject;
        self::$registry = $registry;
        self::$className = $className;
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

        $id = new Id(self::$className, $name, $arguments);

        self::$registry->addRecord($id, $result);

        return $result->getValue();
    }
}
