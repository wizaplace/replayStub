<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace ReplayStub;

trait Replayer
{
    /**
     * @var Registry
     */
    private static $registry;

    /**
     * @var string
     */
    private static $className;

    /**
     * @var ReplayerFactory
     */
    private static $replayerFactory;

    /**
     * @var ?string
     */
    private static $instanceId;

    /**
     * @var ChildrenPolicy
     */
    private static $childrenPolicy;

    /** @noinspection PhpUnusedPrivateMethodInspection */
    private static function ReplayStub_Play(string $name, array $arguments)
    {
        $id = new CallId(self::$className, $name, $arguments, self::$instanceId);

        $result = self::$registry->popRecord($id);
        if (is_null($result)) {
            throw new UnexpectedCall($id);
        }

        $retVal = $result->getValue();

        if (is_object($retVal) && self::$childrenPolicy->shouldBeMocked($retVal)) {
            static $i = 0;
            $retVal = self::$replayerFactory->createReplayer(get_class($retVal), self::$instanceId.' > '.$i);
            $i++;
        }

        return $retVal;
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    private function ReplayStub_Init(string $className, Registry $registry, ReplayerFactory $replayerFactory, ?string $instanceId, ChildrenPolicy $childrenPolicy)
    {
        self::$className = $className;
        self::$replayerFactory = $replayerFactory;
        self::$registry = $registry;
        self::$instanceId = $instanceId;
        self::$childrenPolicy = $childrenPolicy;
    }
}
