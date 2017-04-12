<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace ReplayStub\Test;

use PHPUnit\Framework\TestCase;
use ReplayStub\Call;
use ReplayStub\CallIdSerializer;
use ReplayStub\MockedArg;
use ReplayStub\MockedResult;
use ReplayStub\RecorderFactory;
use ReplayStub\Registry;
use ReplayStub\Result;

class RecorderTest extends TestCase
{
    public function test_simpleCall() {
        $registry = new Registry();
        $factory = new RecorderFactory($registry);

        $recorder = $factory->createRecorder(new ToBeDecorated());
        $this->assertInstanceOf(ToBeDecorated::class, $recorder);
        /** @var ToBeDecorated $recorder */

        $this->assertEquals(4, $recorder->get4());
        // check that the call was registered
        $data = $registry->getData();
        $this->assertCount(1, $data);
        $this->assertEquals(new Call('get4', [], new Result(4)), $data[0]);
    }

    public function test_callWithExtraArgs() {
        $registry = new Registry();
        $factory = new RecorderFactory($registry);

        $recorder = $factory->createRecorder(new ToBeDecorated());
        $this->assertInstanceOf(ToBeDecorated::class, $recorder);
        /** @var ToBeDecorated $recorder */

        $this->assertEquals([1, '2'], $recorder->extra(1, '2'));
        // check that the call was registered
        $data = $registry->getData();
        $this->assertCount(1, $data);
        $this->assertEquals(new Call('extra', [1, '2'], new Result([1, '2'])), $data[0]);
    }

    public function test_simpleChild()
    {
        $registry = new Registry();
        $factory = new RecorderFactory($registry);

        $recorder = $factory->createRecorder(new ToBeDecorated());
        $this->assertInstanceOf(ToBeDecorated::class, $recorder);
        /** @var ToBeDecorated $recorder */

        $result = $recorder->me();
        $this->assertInstanceOf(ToBeDecorated::class, $result);
        $this->assertInstanceOf(ToBeDecorated::class, $result->me());

        // check that the call was registered
        $data = $registry->getData();
        $this->assertCount(2, $data);

        $this->assertEquals('me', $data[0]->getMethod());
        $this->assertEquals([], $data[0]->getArgs());
        $this->assertEmpty($data[0]->getInstanceId());
        $result = $data[0]->getResult();
        $this->assertInstanceOf(MockedResult::class, $result);
        /** @var MockedResult $result */
        $childId = $result->getInstanceId();
        $this->assertNotEmpty($childId);

        $this->assertEquals('me', $data[1]->getMethod());
        $this->assertEquals([], $data[1]->getArgs());
        $this->assertEquals($childId, $data[1]->getInstanceId());
        $this->assertInstanceOf(MockedResult::class, $data[1]->getResult());
    }

    public function test_toString() {
        $registry = new Registry();
        $factory = new RecorderFactory($registry);

        $recorder = $factory->createRecorder(new ToBeDecorated());
        $this->assertInstanceOf(ToBeDecorated::class, $recorder);
        /** @var ToBeDecorated $recorder */

        $this->assertEquals('stringified', (string) $recorder);
        // check that the call was registered
        $data = $registry->getData();
        $this->assertCount(1, $data);
        $this->assertEquals(new Call('__toString', [], new Result('stringified')), $data[0]);
    }

    public function test_staticCall() {
        $registry = new Registry();
        $factory = new RecorderFactory($registry);

        $recorder = $factory->createRecorder(new ToBeDecorated());
        $this->assertInstanceOf(ToBeDecorated::class, $recorder);
        /** @var ToBeDecorated $recorder */

        $this->assertEquals(true, $recorder::staticFunc());
        // check that the call was registered
        $data = $registry->getData();
        $this->assertCount(1, $data);
        $this->assertEquals(new Call('staticFunc', [], new Result(true)), $data[0]);
    }

    public function test_callWithException() {
        $registry = new Registry();
        $factory = new RecorderFactory($registry);

        $recorder = $factory->createRecorder(new ToBeDecorated());
        $this->assertInstanceOf(ToBeDecorated::class, $recorder);
        /** @var ToBeDecorated $recorder */

        $caught = null;
        try {
            $recorder->throwingMethod();
        } catch (ExpectedException $e) {
            $caught = $e;
        }
        $this->assertInstanceOf(ExpectedException::class, $caught);
        // check that the call was registered
        $data = $registry->getData();
        $this->assertCount(1, $data);
        $this->assertEquals('throwingMethod', $data[0]->getMethod());
        $this->assertEquals([], $data[0]->getArgs());
        $this->expectException(ExpectedException::class);
        $data[0]->getResult()->produce();
    }

    public function test_callWithParameter() {
        $registry = new Registry();
        $factory = new RecorderFactory($registry);

        $recorder = $factory->createRecorder(new ToBeDecorated());
        $this->assertInstanceOf(ToBeDecorated::class, $recorder);
        /** @var ToBeDecorated $recorder */

        $this->assertEquals('myString', $recorder->idem('myString'));
        $this->assertEquals('myString2', $recorder->idem('myString2'));

        $data = $registry->getData();
        $this->assertCount(2, $data);
        $this->assertEquals(new Call('idem', ['myString'], new Result('myString')), $data[0]);
        $this->assertEquals(new Call('idem', ['myString2'], new Result('myString2')), $data[1]);
    }

    public function test_typeSafety() {
        $registry = new Registry();
        $factory = new RecorderFactory($registry);

        $recorder = $factory->createRecorder(new ToBeDecorated());
        $this->assertInstanceOf(ToBeDecorated::class, $recorder);
        /** @var ToBeDecorated $recorder */

        $this->expectException(\TypeError::class);
        /** @noinspection PhpStrictTypeCheckingInspection */
        $recorder->idem(42);
    }

    public function test_multipleCalls() {
        $registry = new Registry();
        $factory = new RecorderFactory($registry);

        $recorder = $factory->createRecorder(new ToBeDecorated());
        $this->assertInstanceOf(ToBeDecorated::class, $recorder);
        /** @var ToBeDecorated $recorder */

        $this->assertEquals(0, $recorder->increment());
        $this->assertEquals(1, $recorder->increment());
        $this->assertEquals(2, $recorder->increment());

        $data = $registry->getData();
        $this->assertCount(3, $data);
        $this->assertEquals(new Call('increment', [], new Result(0)), $data[0]);
        $this->assertEquals(new Call('increment', [], new Result(1)), $data[1]);
        $this->assertEquals(new Call('increment', [], new Result(2)), $data[2]);
    }

    public function test_nullable() {
        $registry = new Registry();
        $factory = new RecorderFactory($registry);

        $recorder = $factory->createRecorder(new ToBeDecorated());
        $this->assertInstanceOf(ToBeDecorated::class, $recorder);
        /** @var ToBeDecorated $recorder */

        $this->assertEquals(null, $recorder->nullable(null));
        // check that the call was registered
        $data = $registry->getData();
        $this->assertCount(1, $data);
        $this->assertEquals(new Call('nullable', [null], new Result(null)), $data[0]);
    }

    public function test_recordYourself() {
        $registry = new Registry();
        $factory = new RecorderFactory($registry);

        $recorder = $factory->createRecorder(new ToBeDecorated());
        $this->assertInstanceOf(ToBeDecorated::class, $recorder);
        /** @var ToBeDecorated $recorder */

        $this->assertEquals(null, $recorder->autoConsumption($recorder));
        // check that the call was registered
        $data = $registry->getData();
        $this->assertCount(1, $data);
        $this->assertEquals(new Call('autoConsumption', [new MockedArg(null)], new Result(null)), $data[0]);
    }

    public function test_dateTimeImmutable() {
        $this->markTestIncomplete('Can\'t create proxy for internals yet... @FIXME');
        $registry = new Registry();
        $factory = new RecorderFactory($registry);

        $factory->createRecorder(new \DateTimeImmutable());
    }
}
