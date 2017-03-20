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

    public function __construct($decoratedObject, Registry $registry)
    {
        $this->decoratedObject = $decoratedObject;
        $this->registry = $registry;
    }

    public function __call(string $name, array $arguments)
    {
        $thrown = null;
        $returned = null;
        try {
            $returned = call_user_func_array([$this->decoratedObject, $name], $arguments);
        } catch (\Exception $e) {
            $thrown = $e;
        }

        $result = new Result($returned, $thrown);

        $id = new Id(get_called_class(), $name, $arguments);
        var_export($id);exit;

        $this->registry->addRecord($id, $result);

        return $result->getValue();
    }
}
