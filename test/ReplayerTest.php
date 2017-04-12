<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace ReplayStub\Test;

use Mockery\Exception\InvalidCountException;
use Mockery\Exception\InvalidOrderException;
use PHPUnit\Framework\TestCase;
use ReplayStub\Call;
use ReplayStub\MockedArg;
use ReplayStub\MockedResult;
use ReplayStub\Registry;
use ReplayStub\ReplayerFactory;
use ReplayStub\Result;

class ReplayerTest extends TestCase
{
    public function test_simpleCall() {
        $registry = new Registry();
        $registry->addCall(new Call('get4', [], new Result(4)));
        $factory = new ReplayerFactory($registry);

        $replayer = $factory->createReplayer(ToBeImplemented::class);
        $this->assertInstanceOf(ToBeImplemented::class, $replayer);
        /** @var ToBeImplemented $replayer */

        $this->assertEquals(4, $replayer->get4());

        $this->assertEquals(4, $replayer->get4());
        $this->expectException(InvalidCountException::class);
        $factory->close();
    }

    public function test_toString() {
        $registry = new Registry();
        $registry->addCall(new Call('__toString', [], new Result('stringified')));
        $factory = new ReplayerFactory($registry);

        $replayer = $factory->createReplayer(ToBeImplemented::class);
        $this->assertInstanceOf(ToBeImplemented::class, $replayer);
        /** @var ToBeImplemented $replayer */

        $this->assertEquals('stringified', (string) $replayer);
    }

    public function test_argsTypeSafety() {
        $registry = new Registry();
        $registry->addCall(new Call('idem', [42], new Result(42)));
        $factory = new ReplayerFactory($registry);

        $replayer = $factory->createReplayer(ToBeImplemented::class);
        $this->assertInstanceOf(ToBeImplemented::class, $replayer);
        /** @var ToBeImplemented $replayer */

        $this->expectException(\TypeError::class);
        /** @noinspection PhpStrictTypeCheckingInspection */
        $replayer->idem(42);
    }

    public function test_returnTypeSafety() {
        $registry = new Registry();
        $registry->addCall(new Call('idem', ['42'], new Result([42])));
        $factory = new ReplayerFactory($registry);

        $replayer = $factory->createReplayer(ToBeImplemented::class);
        $this->assertInstanceOf(ToBeImplemented::class, $replayer);
        /** @var ToBeImplemented $replayer */

        $this->expectException(\TypeError::class);
        $replayer->idem('42');
    }

    public function test_returnTypeSafety_withImplicitCast() {
        $registry = new Registry();
        $registry->addCall(new Call('idem', ['42'], new Result(42)));
        $factory = new ReplayerFactory($registry);

        $replayer = $factory->createReplayer(ToBeImplemented::class);
        $this->assertInstanceOf(ToBeImplemented::class, $replayer);
        /** @var ToBeImplemented $replayer */

        $this->assertTrue(is_string($replayer->idem('42')));
    }

    public function test_callWithParameter() {
        $registry = new Registry();
        $registry->addCall(new Call('idem', ['myString'], new Result('myString')));
        $registry->addCall(new Call('idem', ['myString2'], new Result('myString2')));
        $factory = new ReplayerFactory($registry);

        $replayer = $factory->createReplayer(ToBeImplemented::class);
        $this->assertInstanceOf(ToBeImplemented::class, $replayer);
        /** @var ToBeImplemented $replayer */

        $this->assertEquals('myString', $replayer->idem('myString'));
        $this->assertEquals('myString2', $replayer->idem('myString2'));
    }

    public function test_multipleCalls() {
        $registry = new Registry();
        $registry->addCall(new Call('increment', [], new Result(0)));
        $registry->addCall(new Call('increment', [], new Result(1)));
        $registry->addCall(new Call('increment', [], new Result(2)));
        $factory = new ReplayerFactory($registry);

        $replayer = $factory->createReplayer(ToBeImplemented::class);
        $this->assertInstanceOf(ToBeImplemented::class, $replayer);
        /** @var ToBeImplemented $replayer */

        $this->assertEquals(0, $replayer->increment());
        $this->assertEquals(1, $replayer->increment());
        $this->assertEquals(2, $replayer->increment());

        $this->expectException(InvalidOrderException::class); // @FIXME : wrong exception, should be a NoMatchingExpectationException
        $replayer->increment();
    }

    public function test_callWithException() {
        $registry = new Registry();
        $registry->addCall(new Call('throwingMethod', [], new Result(null, new ExpectedException())));
        $factory = new ReplayerFactory($registry);

        $replayer = $factory->createReplayer(ToBeImplemented::class);
        $this->assertInstanceOf(ToBeImplemented::class, $replayer);
        /** @var ToBeImplemented $replayer */

        $this->expectException(ExpectedException::class);
        $replayer->throwingMethod();
    }

    public function test_simpleChild()
    {
        $registry = new Registry();
        $childId = uniqid();
        $registry->addCall(new Call('me2', [], new MockedResult($childId, new ToBeDecorated())));
        $registry->addCall(new Call('__toString', [], new Result('child_stringified'), $childId));
        $factory = new ReplayerFactory($registry);

        $replayer = $factory->createReplayer(ToBeImplemented::class);
        $this->assertInstanceOf(ToBeImplemented::class, $replayer);
        /** @var ToBeImplemented $replayer */

        $result = $replayer->me2();
        $this->assertInstanceOf(ToBeImplemented::class, $result);

        $this->assertEquals('child_stringified', (string)$result);
    }
    
    public function test_lawAndOrder()
    {
        $registry = new Registry();
        $registry->addCall(new Call('idem', ['myString'], new Result('myString')));
        $registry->addCall(new Call('get4', [], new Result(4)));
        $factory = new ReplayerFactory($registry);

        $replayer = $factory->createReplayer(ToBeImplemented::class);
        $this->assertInstanceOf(ToBeImplemented::class, $replayer);
        /** @var ToBeImplemented $replayer */

        $this->assertEquals(4, $replayer->get4());
        $this->expectException(InvalidOrderException::class);
        $this->assertEquals('myString', $replayer->idem('myString'));
    }

    public function test_replayYourself()
    {
        $registry = new Registry();
        $registry->addCall(new Call('autoConsumption', [new MockedArg(null)], new Result(null)));
        $factory = new ReplayerFactory($registry);

        $replayer = $factory->createReplayer(ToBeImplemented::class);
        $this->assertInstanceOf(ToBeImplemented::class, $replayer);
        /** @var ToBeImplemented $replayer */

        $replayer->autoConsumption($replayer);
    }
}
