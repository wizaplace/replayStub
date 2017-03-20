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
use RePHPlay\Mocker;
use RePHPlay\Registry;
use RePHPlay\Serializer;

class MockerTest extends TestCase
{
    public function testCreateRecorder() {
        $registry = new Registry(new Serializer());
        $mocker = new Mocker($registry);
        $recorder = $mocker->createRecorder(new ToBeDecorated());

        $this->assertEquals(4, $recorder->method1());
        $result = $registry->popRecord(new Id(ToBeDecorated::class, 'method1', []));

        $this->assertNotNull($result);
        $this->assertEquals(4, $result->getValue());
    }
}

class ToBeDecorated {
    public function method1() {
        return 4;
    }
};
