<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace ReplayStub;

/**
 * Serialize `CallId`s
 * Extend it if you want to filter some data, to allow for a looser matching.
 */
class CallIdSerializer
{
    public function serialize(CallId $id): string
    {
        return serialize($id);
    }
}
