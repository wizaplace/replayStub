<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace RePHPlay;

use ReflectionMethod;

class Mocker
{
    /**
     * @var Registry
     */
    private $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }


    public function createRecorder($decoratedObject, ?string $instanceId = null)
    {
        if(!is_object($decoratedObject)) {
            throw new \InvalidArgumentException('$decoratedObject must be an object, '.gettype($decoratedObject).' given.');
        }
        $reflection = new \ReflectionClass($decoratedObject);

        $phpClass =<<<EOT
return new class(\$decoratedObject, \$this->registry, "{$reflection->getName()}", \$this) extends {$reflection->getName()} {
    use \RePHPlay\Recorder;

EOT;
        foreach($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            $static = $method->isStatic() ? 'static ' : '';
            $args = [];
            foreach ($method->getParameters() as $parameter) {
                $args[] = "{$parameter->getType()} \${$parameter->getName()}";
            }
            $args = implode(', ', $args);
            $phpClass .= "    public {$static}function {$method->getName()}($args) ";
            if ($method->hasReturnType()) {
                $phpClass .= ": {$method->getReturnType()} ";
            }
            $phpClass .= "{ return self::RePHPlay_Record(__FUNCTION__, func_get_args()); }\n";
        }
        $phpClass .= '};';
        return eval($phpClass);
    }
}
