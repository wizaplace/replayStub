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

class RecorderFactory
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


    public function createRecorder($decoratedObject, /** @noinspection PhpUnusedParameterInspection */
                                   ?string $instanceId = null)
    {
        if(!is_object($decoratedObject)) {
            throw new \InvalidArgumentException('$decoratedObject must be an object, '.gettype($decoratedObject).' given.');
        }
        $reflection = new \ReflectionClass($decoratedObject);

        $phpClass =<<<EOT
return new class(\$decoratedObject, \$this->registry, "{$reflection->getName()}", \$this, \$instanceId, \$this->childrenPolicy) extends {$reflection->getName()} {
    use \RePHPlay\Recorder;
    
    public function __construct()
    {
        call_user_func_array([\$this, 'RePHPlay_Init'], func_get_args());
    }

EOT;
        foreach($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->isConstructor() || $method->isDestructor()) {
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
            $phpClass .= "{ return self::RePHPlay_Record(__FUNCTION__, func_get_args()); }\n";
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
