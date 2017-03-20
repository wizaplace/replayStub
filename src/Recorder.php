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
    private $records = [];

    private $decoratedObject;

    private $registry;

    private $className;

    public function __construct($decoratedObject, Registry $registry, string $className)
    {
        $this->decoratedObject = $decoratedObject;
        $this->registry = $registry;
        $this->className = $className;
    }

    private function RePHPlay_Record(string $name, array $arguments)
    {
        $thrown = null;
        $returned = null;
        try {
            $returned = call_user_func_array([$this->decoratedObject, $name], $arguments);
        } catch (\Exception $e) {
            $thrown = $e;
        }

        $result = new Result($returned, $thrown);

        $id = new Id($this->className, $name, $arguments);

        $this->registry->addRecord($id, $result);

        return $result->getValue();
    }
}
