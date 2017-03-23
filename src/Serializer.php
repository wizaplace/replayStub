<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace ReplayStub;

class Serializer
{
    public function serialize(CallId $id): string
    {
        return serialize($id);
    }
}
