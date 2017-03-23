<?php
/**
 * Created by PhpStorm.
 * User: hectorj
 * Date: 21/03/17
 * Time: 20:49
 */

namespace ReplayStub\ChildrenPolicy;


use ReplayStub\ChildrenPolicy;

class MockNothing implements ChildrenPolicy
{
    public function shouldBeMocked($object): bool
    {
        return false;
    }
}
