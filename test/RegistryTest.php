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
use ReplayStub\Registry;
use ReplayStub\Result;

class RegistryTest extends TestCase
{
    public function test_serialization()
    {
        $registry = new Registry();

        $call = new Call('myMethod', [1, 'a', ['b']], new Result(4));
        $callException = new Call('myMethod2', [], new Result(null, new ExpectedException()));

        $registry->addCall($call);
        $registry->addCall($callException);

        $registry = unserialize(serialize($registry), [Registry::class]);
        /** @var Registry $registry */

        $data = $registry->getData();
        $this->assertCount(2, $data);
        $this->assertEquals(4, $data[0]->getResult()->produce());

        $this->expectException(ExpectedException::class);
        $data[1]->getResult()->produce();
    }

    public function test_json_serialization()
    {
        $registry = new Registry();

        $call = new Call('myMethod', [1, 'a', ['b']], new Result(4));
        $callException = new Call('myMethod2', [], new Result(null, new ExpectedException()));

        $registry->addCall($call);
        $registry->addCall($callException);

        $serialized = json_encode($registry, JSON_PRETTY_PRINT);
        $this->assertNotEmpty($serialized);
    }
}
