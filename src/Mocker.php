<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace RePHPlay;

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


    public function createRecorder($decoratedObject)
    {
        if(!is_object($decoratedObject)) {
            throw new \InvalidArgumentException('$decoratedObject must be an object, '.gettype($decoratedObject).' given.');
        }
        $reflection = new \ReflectionClass($decoratedObject);

        $phpClass =<<<EOT
return new class(\$decoratedObject, \$this->registry) extends {$reflection->getName()} {
    use \RePHPlay\Recorder;
};
EOT;
        return eval($phpClass);
    }
}
