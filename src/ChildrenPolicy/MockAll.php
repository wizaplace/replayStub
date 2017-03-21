<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace RePHPlay\ChildrenPolicy;


use RePHPlay\ChildrenPolicy;

class MockAll implements ChildrenPolicy
{
    public function shouldBeMocked($object): bool
    {
        return true;
    }
}