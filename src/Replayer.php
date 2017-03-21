<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace RePHPlay;

trait Replayer
{
    private static $registry;

    private static $className;

    private static $replayerFactory;

    private static $instanceId;

    public function __construct(string $className, Registry $registry, ReplayerFactory $replayerFactory, ?string $instanceId = null)
    {
        self::$className = $className;
        self::$replayerFactory = $replayerFactory;
        self::$registry = $registry;
        self::$instanceId = $instanceId;
    }

    private static function RePHPlay_Play(string $name, array $arguments)
    {
        $id = new Id(self::$className, $name, $arguments, self::$instanceId);

        $result = self::$registry->popRecord($id);
        if (is_null($result)) {
            throw new UnexpectedCall("Unexpected call to $name");
        }

        $retVal = $result->getValue();

        if (is_object($retVal)) {
            static $i = 0;
            $retVal = self::$replayerFactory->createReplayer(get_class($retVal), self::$instanceId.' > '.$i);
            $i++;
        }

        return $retVal;
    }
}
