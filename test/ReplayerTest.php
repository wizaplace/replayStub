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
use RePHPlay\Result;
use RePHPlay\Serializer;
use RePHPlay\UnexpectedCall;

class ReplayerTest extends TestCase
{
    public function test_simpleCall() {
        $registry = new Registry(new Serializer());
        $registry->addRecord(new Id(ToBeImplemented::class, 'get4', []), new Result(4));
        $factory = new ReplayerFactory($registry);

        $replayer = $factory->createReplayer(ToBeImplemented::class);
        $this->assertInstanceOf(ToBeImplemented::class, $replayer);
        /**
         * @var ToBeImplemented $replayer
         */

        $this->assertEquals(4, $replayer->get4());

        $this->expectException(UnexpectedCall::class);
        $replayer->get4();
    }

    public function test_toString() {
        $registry = new Registry(new Serializer());
        $registry->addRecord(new Id(ToBeImplemented::class, '__toString', []), new Result('stringified'));
        $factory = new ReplayerFactory($registry);

        $replayer = $factory->createReplayer(ToBeImplemented::class);
        $this->assertInstanceOf(ToBeImplemented::class, $replayer);
        /**
         * @var ToBeImplemented $replayer
         */

        $this->assertEquals('stringified', (string) $replayer);
    }

    public function test_argsTypeSafety() {
        $registry = new Registry(new Serializer());
        $registry->addRecord(new Id(ToBeImplemented::class, 'idem', [42]), new Result(42));
        $factory = new ReplayerFactory($registry);

        $replayer = $factory->createReplayer(ToBeImplemented::class);
        $this->assertInstanceOf(ToBeImplemented::class, $replayer);
        /**
         * @var ToBeImplemented $replayer
         */

        $this->expectException(\TypeError::class);
        $replayer->idem(42);
    }

    public function test_returnTypeSafety() {
        $registry = new Registry(new Serializer());
        $registry->addRecord(new Id(ToBeImplemented::class, 'idem', ['42']), new Result([42]));
        $factory = new ReplayerFactory($registry);

        $replayer = $factory->createReplayer(ToBeImplemented::class);
        $this->assertInstanceOf(ToBeImplemented::class, $replayer);
        /**
         * @var ToBeImplemented $replayer
         */

        $this->expectException(\TypeError::class);
        $replayer->idem('42');
    }

    public function test_returnTypeSafety_withImplicitCast() {
        $registry = new Registry(new Serializer());
        $registry->addRecord(new Id(ToBeImplemented::class, 'idem', ['42']), new Result(42));
        $factory = new ReplayerFactory($registry);

        $replayer = $factory->createReplayer(ToBeImplemented::class);
        $this->assertInstanceOf(ToBeImplemented::class, $replayer);
        /**
         * @var ToBeImplemented $replayer
         */

        $this->assertTrue(is_string($replayer->idem('42')));
    }

    public function test_callWithParameter() {
        $registry = new Registry(new Serializer());
        $registry->addRecord(new Id(ToBeImplemented::class, 'idem', ['myString']), new Result('myString'));
        $registry->addRecord(new Id(ToBeImplemented::class, 'idem', ['myString2']), new Result('myString2'));
        $factory = new ReplayerFactory($registry);

        $replayer = $factory->createReplayer(ToBeImplemented::class);
        $this->assertInstanceOf(ToBeImplemented::class, $replayer);
        /**
         * @var ToBeImplemented $replayer
         */

        $this->assertEquals('myString', $replayer->idem('myString'));
        $this->assertEquals('myString2', $replayer->idem('myString2'));
    }

    public function test_multipleCalls() {
        $registry = new Registry(new Serializer());
        $registry->addRecord(new Id(ToBeImplemented::class, 'increment', []), new Result(0));
        $registry->addRecord(new Id(ToBeImplemented::class, 'increment', []), new Result(1));
        $registry->addRecord(new Id(ToBeImplemented::class, 'increment', []), new Result(2));
        $factory = new ReplayerFactory($registry);

        $replayer = $factory->createReplayer(ToBeImplemented::class);
        $this->assertInstanceOf(ToBeImplemented::class, $replayer);
        /**
         * @var ToBeImplemented $replayer
         */

        $this->assertEquals(0, $replayer->increment());
        $this->assertEquals(1, $replayer->increment());
        $this->assertEquals(2, $replayer->increment());

        $this->expectException(UnexpectedCall::class);
        $replayer->increment();
    }

    public function test_callWithException() {
        $registry = new Registry(new Serializer());
        $registry->addRecord(new Id(ToBeImplemented::class, 'throwingMethod', []), new Result(null, new ExpectedException()));
        $factory = new ReplayerFactory($registry);

        $replayer = $factory->createReplayer(ToBeImplemented::class);
        $this->assertInstanceOf(ToBeImplemented::class, $replayer);
        /**
         * @var ToBeImplemented $replayer
         */

        $this->expectException(ExpectedException::class);
        $replayer->throwingMethod();
    }
}

interface ToBeImplemented {
    public function get4();

    public function extra() : array;

    public function throwingMethod();

    public function idem(string $str) : string;

    public function increment();

    public static function staticFunc() : bool;

    public function me() : self;

    public function __toString() : string;
}
