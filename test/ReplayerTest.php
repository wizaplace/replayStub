<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace RePHPlay\Test;

use PHPUnit\Framework\TestCase;
use RePHPlay\Id;
use RePHPlay\RecorderFactory;
use RePHPlay\Registry;
use RePHPlay\ReplayerFactory;
use RePHPlay\Serializer;
use RePHPlay\UnexpectedCall;

class ReplayerTest extends TestCase
{
    public function testRecorder_emptyRegistry() {
        $registry = new Registry(new Serializer());
        $factory = new ReplayerFactory($registry);

        $replayer = $factory->createReplayer(ToBeDecorated::class);
        $this->assertInstanceOf(ToBeDecorated::class, $replayer);
        /**
         * @var ToBeDecorated $replayer
         */

        $this->expectException(UnexpectedCall::class);
        $replayer->get4();
    }
}
