<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace RePHPlay;

use ReflectionMethod;
use RePHPlay\ChildrenPolicy\MockAll;

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
        $reflection = new \ReflectionClass($className);

        $extends = $reflection->isInterface() ? 'implements' : 'extends';

        $phpClass =<<<EOT
return new class("{$reflection->getName()}", \$this->registry, \$this, \$instanceId, \$this->childrenPolicy) $extends {$reflection->getName()} {
    use \RePHPlay\Replayer;

    public function __construct()
    {
        call_user_func_array([\$this, 'RePHPlay_Init'], func_get_args());
    }

EOT;

        foreach($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->isConstructor()) {
                continue;
            }
            $static = $method->isStatic() ? 'static ' : '';
            $args = [];
            foreach ($method->getParameters() as $parameter) {
                $type = self::formatArgType($parameter->getType(), $reflection->getName());
                $arg = "{$type} \${$parameter->getName()}";
                if($parameter->isDefaultValueAvailable()) {
                    if ($parameter->isDefaultValueConstant()) {
                        $arg .=  ' = '.$parameter->getDefaultValueConstantName();
                    } else {
                        $arg .=  ' = '.var_export($parameter->getDefaultValue(), true);
                    }
                }
                $args[] = $arg;
            }
            $args = implode(', ', $args);
            $phpClass .= "    public {$static}function {$method->getName()}($args) ";
            if ($method->hasReturnType()) {
                $type = self::formatArgType($method->getReturnType(), $reflection->getName());
                $phpClass .= ": {$type} ";
            }
            $phpClass .= "{ return self::RePHPlay_Play(__FUNCTION__, func_get_args()); }\n";
        }
        $phpClass .= '};';
        return eval($phpClass);
    }

    private static function formatArgType(?\ReflectionType $type, string $className) : string {
        $str = (string) $type;
        return $str === 'self' ? $className : $str;
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
