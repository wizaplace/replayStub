<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace ReplayStub;

use ReflectionMethod;
use ReplayStub\ChildrenPolicy\MockAll;

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
return new class(\$decoratedObject, \$this->registry, \$this, \$instanceId, \$this->childrenPolicy) extends {$reflection->getName()} {
    use \ReplayStub\Recorder;
    
    public function __construct()
    {
        call_user_func_array([\$this, 'ReplayStub_Init'], func_get_args());
    }

EOT;
        foreach($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->isConstructor() || $method->isDestructor()) {
                continue;
            }
            $static = $method->isStatic() ? 'static ' : '';
            $args = [];
            foreach ($method->getParameters() as $parameter) {
                $reflectionType = $parameter->getType();
                $type = $reflectionType ? self::formatArgType($reflectionType, $reflection->getName()) .' ' : '';
                $arg = "{$type}\${$parameter->getName()}";
                if($parameter->isDefaultValueAvailable()) { // @FIXME : this returns false negatives for internals
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
            $isVoid = false;
            if ($method->hasReturnType()) {
                $type = self::formatArgType($method->getReturnType(), $reflection->getName());
                $isVoid = $type === 'void';
                $phpClass .= ": {$type} ";
            }
            $returnStr = $isVoid ? '' : 'return ';
            $phpClass .= "{ {$returnStr}self::ReplayStub_Record(__FUNCTION__, func_get_args()); }\n";
        }
        $phpClass .= '};';
        return eval($phpClass);
    }

    private static function formatArgType(\ReflectionType $type, string $className) : string {
        $str = (string) $type;
        $str = $str === 'self' ? $className : $str;
        if($type->allowsNull()) {
            $str = '?'.$str;
        }
        return $str;
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
