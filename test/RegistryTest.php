<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace ReplayStub\Test;

use PHPUnit\Framework\TestCase;
use ReplayStub\CallId;
use ReplayStub\Registry;
use ReplayStub\Result;
use ReplayStub\CallIdSerializer;

class RegistryTest extends TestCase
{
    public function test_serialization()
    {
        $registry = new Registry(new CallIdSerializer());

        $id = new CallId(self::class, 'myMethod', []);
        $idException = new CallId(self::class, 'myMethod2', []);

        $registry->addRecord($id, new Result(4));
        $registry->addRecord($idException, new Result(null, new ExpectedException()));

        $registry = unserialize(serialize($registry));

        $this->assertEquals(4, $registry->popRecord($id)->getValue());

        $this->expectException(ExpectedException::class);
        $registry->popRecord($idException)->getValue();
    }
}
