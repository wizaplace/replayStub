<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace RePHPlay\Test;

use RePHPlay\Id;
use RePHPlay\Registry;
use PHPUnit\Framework\TestCase;
use RePHPlay\Result;
use RePHPlay\Serializer;

class RegistryTest extends TestCase
{
    public function test_serialization()
    {
        $registry = new Registry(new Serializer());

        $id = new Id(self::class, 'myMethod', []);
        $idException = new Id(self::class, 'myMethod2', []);

        $registry->addRecord($id, new Result(4));
        $registry->addRecord($idException, new Result(null, new ExpectedException()));

        $registry = unserialize(serialize($registry));

        $this->assertEquals(4, $registry->popRecord($id)->getValue());

        $this->expectException(ExpectedException::class);
        $registry->popRecord($idException)->getValue();
    }
}