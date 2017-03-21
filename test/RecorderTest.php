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
use RePHPlay\Serializer;

class RecorderTest extends TestCase
{
    public function test_simpleCall() {
        $registry = new Registry(new Serializer());
        $factory = new RecorderFactory($registry);

        $recorder = $factory->createRecorder(new ToBeDecorated());
        $this->assertInstanceOf(ToBeDecorated::class, $recorder);
        /**
         * @var ToBeDecorated $recorder
         */

        $this->assertEquals(4, $recorder->get4());
        // check that the record was registered
        $result = $registry->popRecord(new Id(ToBeDecorated::class, 'get4', []));
        $this->assertNotNull($result);
        $this->assertEquals(4, $result->getValue());
        // check that there is no more records
        $result = $registry->popRecord(new Id(ToBeDecorated::class, 'get4', []));
        $this->assertNull($result);
    }

    public function test_callWithExtraArgs() {
        $registry = new Registry(new Serializer());
        $factory = new RecorderFactory($registry);

        $recorder = $factory->createRecorder(new ToBeDecorated());
        $this->assertInstanceOf(ToBeDecorated::class, $recorder);
        /**
         * @var ToBeDecorated $recorder
         */

        $this->assertEquals([1, '2'], $recorder->extra(1, '2'));
        // check that the record was registered
        $result = $registry->popRecord(new Id(ToBeDecorated::class, 'extra', [1, '2']));
        $this->assertNotNull($result);
        $this->assertEquals([1, '2'], $result->getValue());
        // check that there is no more records
        $result = $registry->popRecord(new Id(ToBeDecorated::class, 'extra', [1, '2']));
        $this->assertNull($result);
    }

    public function test_simpleRecursion() {
        $registry = new Registry(new Serializer());
        $factory = new RecorderFactory($registry);

        $recorder = $factory->createRecorder(new ToBeDecorated());
        $this->assertInstanceOf(ToBeDecorated::class, $recorder);
        /**
         * @var ToBeDecorated $recorder
         */

        $result = $recorder->me();
        $this->assertInstanceOf(ToBeDecorated::class, $result);
        $this->assertInstanceOf(ToBeDecorated::class, $result->me());

        // check that the record was registered
        $result = $registry->popRecord(new Id(ToBeDecorated::class, 'me', []));
        $this->assertNotNull($result);
        $this->assertInstanceOf(ToBeDecorated::class, $result->getValue());
        // check that there is no more records
        $result = $registry->popRecord(new Id(ToBeDecorated::class, 'me', []));
        $this->assertNull($result);

        // check that the record of the recursive mock was registered
        $result = $registry->popRecord(new Id(ToBeDecorated::class, 'me', [], ' > 0'));
        $this->assertNotNull($result);
        $this->assertInstanceOf(ToBeDecorated::class, $result->getValue());
        // check that there is no more records
        $result = $registry->popRecord(new Id(ToBeDecorated::class, 'me', [], ' > 0'));
        $this->assertNull($result);
    }

    public function test_toString() {
        $registry = new Registry(new Serializer());
        $factory = new RecorderFactory($registry);

        $recorder = $factory->createRecorder(new ToBeDecorated());
        $this->assertInstanceOf(ToBeDecorated::class, $recorder);
        /**
         * @var ToBeDecorated $recorder
         */

        $this->assertEquals('stringified', (string) $recorder);
        // check that the record was registered
        $result = $registry->popRecord(new Id(ToBeDecorated::class, '__toString', []));
        $this->assertNotNull($result);
        $this->assertEquals('stringified', $result->getValue());
        // check that there is no more records
        $result = $registry->popRecord(new Id(ToBeDecorated::class, '__toString', []));
        $this->assertNull($result);
    }

    public function test_staticCall() {
        $registry = new Registry(new Serializer());
        $factory = new RecorderFactory($registry);

        $recorder = $factory->createRecorder(new ToBeDecorated());
        $this->assertInstanceOf(ToBeDecorated::class, $recorder);
        /**
         * @var ToBeDecorated $recorder
         */

        $this->assertEquals(true, $recorder::staticFunc());
        // check that the record was registered
        $result = $registry->popRecord(new Id(ToBeDecorated::class, 'staticFunc', []));
        $this->assertNotNull($result);
        $this->assertEquals(true, $result->getValue());
        // check that there is no more records
        $result = $registry->popRecord(new Id(ToBeDecorated::class, 'staticFunc', []));
        $this->assertNull($result);
    }

    public function test_callWithException() {
        $registry = new Registry(new Serializer());
        $factory = new RecorderFactory($registry);

        $recorder = $factory->createRecorder(new ToBeDecorated());
        $this->assertInstanceOf(ToBeDecorated::class, $recorder);
        /**
         * @var ToBeDecorated $recorder
         */

        $caught = null;
        try {
            $recorder->throwingMethod();
        } catch (ExpectedException $e) {
            $caught = $e;
        }
        $this->assertInstanceOf(ExpectedException::class, $caught);
        // check that the record was registered
        $result = $registry->popRecord(new Id(ToBeDecorated::class, 'throwingMethod', []));
        $this->assertNotNull($result);
        // check that getting the value from the record throws the same exception again
        $caught = null;
        try {
            $result->getValue();
        } catch (ExpectedException $e) {
            $caught = $e;
        }
        $this->assertInstanceOf(ExpectedException::class, $caught);
    }

    public function test_callWithParameter() {
        $registry = new Registry(new Serializer());
        $factory = new RecorderFactory($registry);

        $recorder = $factory->createRecorder(new ToBeDecorated());
        $this->assertInstanceOf(ToBeDecorated::class, $recorder);
        /**
         * @var ToBeDecorated $recorder
         */

        $this->assertEquals('myString', $recorder->idem('myString'));
        $this->assertEquals('myString2', $recorder->idem('myString2'));

        // check that the first record was registered
        $result = $registry->popRecord(new Id(ToBeDecorated::class, 'idem', ['myString']));
        $this->assertNotNull($result);
        $this->assertEquals('myString', $result->getValue());
        // check that there is no more records
        $result = $registry->popRecord(new Id(ToBeDecorated::class, 'idem', ['myString']));
        $this->assertNull($result);

        // check that the second record was registered
        $result = $registry->popRecord(new Id(ToBeDecorated::class, 'idem', ['myString2']));
        $this->assertNotNull($result);
        $this->assertEquals('myString2', $result->getValue());
        // check that there is no more records
        $result = $registry->popRecord(new Id(ToBeDecorated::class, 'idem', ['myString2']));
        $this->assertNull($result);
    }

    public function test_typeSafety() {
        $registry = new Registry(new Serializer());
        $factory = new RecorderFactory($registry);

        $recorder = $factory->createRecorder(new ToBeDecorated());
        $this->assertInstanceOf(ToBeDecorated::class, $recorder);
        /**
         * @var ToBeDecorated $recorder
         */

        $this->expectException(\TypeError::class);
        $recorder->idem(42);
    }

    public function test_multipleCalls() {
        $registry = new Registry(new Serializer());
        $factory = new RecorderFactory($registry);

        $recorder = $factory->createRecorder(new ToBeDecorated());
        $this->assertInstanceOf(ToBeDecorated::class, $recorder);
        /**
         * @var ToBeDecorated $recorder
         */

        $this->assertEquals(0, $recorder->increment());
        $this->assertEquals(1, $recorder->increment());
        $this->assertEquals(2, $recorder->increment());

        // check that the first record was registered
        $result = $registry->popRecord(new Id(ToBeDecorated::class, 'increment', []));
        $this->assertNotNull($result);
        $this->assertEquals(0, $result->getValue());

        // check that the second record was registered
        $result = $registry->popRecord(new Id(ToBeDecorated::class, 'increment', []));
        $this->assertNotNull($result);
        $this->assertEquals(1, $result->getValue());

        // check that the third record was registered
        $result = $registry->popRecord(new Id(ToBeDecorated::class, 'increment', []));
        $this->assertNotNull($result);
        $this->assertEquals(2, $result->getValue());

        // check that there is no more records
        $result = $registry->popRecord(new Id(ToBeDecorated::class, 'get4', []));
        $this->assertNull($result);
    }
}

class ToBeDecorated {
    public function get4() {
        return 4;
    }

    public function extra() : array {
        return func_get_args();
    }

    public function throwingMethod() {
        throw new ExpectedException();
    }

    public function idem(string $str) : string {
        return $str;
    }

    public function increment() {
        static $i = 0;
        return $i++;
    }

    public static function staticFunc() : bool {
        return true;
    }

    public function me() : self {
        return $this;
    }

    public function __toString() : string
    {
        return 'stringified';
    }
};

class ExpectedException extends \Exception {}
