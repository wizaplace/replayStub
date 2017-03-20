<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace RePHPlay\Test;

use PHPUnit\Framework\Exception;
use PHPUnit\Framework\TestCase;
use RePHPlay\Id;
use RePHPlay\Mocker;
use RePHPlay\Registry;
use RePHPlay\Serializer;

class MockerTest extends TestCase
{
    public function testRecorder_simpleCall() {
        $registry = new Registry(new Serializer());
        $mocker = new Mocker($registry);

        $recorder = $mocker->createRecorder(new ToBeDecorated());
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

    public function testRecorder_staticCall() {
        $registry = new Registry(new Serializer());
        $mocker = new Mocker($registry);

        $recorder = $mocker->createRecorder(new ToBeDecorated());
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

    public function testRecorder_callWithException() {
        $registry = new Registry(new Serializer());
        $mocker = new Mocker($registry);

        $recorder = $mocker->createRecorder(new ToBeDecorated());
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

    public function testRecorder_callWithParameter() {
        $registry = new Registry(new Serializer());
        $mocker = new Mocker($registry);

        $recorder = $mocker->createRecorder(new ToBeDecorated());
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

    public function testRecorder_typeSafety() {
        $registry = new Registry(new Serializer());
        $mocker = new Mocker($registry);

        $recorder = $mocker->createRecorder(new ToBeDecorated());
        $this->assertInstanceOf(ToBeDecorated::class, $recorder);
        /**
         * @var ToBeDecorated $recorder
         */

        $this->expectException(\TypeError::class);
        $recorder->idem(42);
    }

    public function testRecorder_multipleCalls() {
        $registry = new Registry(new Serializer());
        $mocker = new Mocker($registry);

        $recorder = $mocker->createRecorder(new ToBeDecorated());
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
};

class ExpectedException extends \Exception {}
