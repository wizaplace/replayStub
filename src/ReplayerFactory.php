<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace RePHPlay;

use ReflectionMethod;

class ReplayerFactory
{
    /**
     * @var Registry
     */
    private $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function createReplayer(string $className, ?string $instanceId = null)
    {
        $reflection = new \ReflectionClass($className);

        $extends = $reflection->isInterface() ? 'implements' : 'extends';

        $phpClass =<<<EOT
return new class("{$reflection->getName()}", \$this->registry, \$this, \$instanceId) $extends {$reflection->getName()} {
    use \RePHPlay\Replayer;

EOT;

        foreach($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
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
}
