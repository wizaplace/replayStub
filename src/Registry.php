<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace ReplayStub;

class Registry implements \JsonSerializable, \Serializable
{
    /**
     * @var Call[]
     */
    private $data = [];

    public function addCall(Call $call)
    {
        $this->data[] = $call;
    }


    /**
     * @return Call[]
     */
    public function getData() : array
    {
        return $this->data;
    }

    public function jsonSerialize(): array
    {
        return $this->data;
    }

    public function serialize(): string
    {
        return \serialize($this->data);
    }

    public function unserialize($serialized): void
    {
        $calls = \unserialize($serialized, [
            'allowed_classes' => [ Call::class ],
        ]);

        foreach ($calls as $call) {
            $this->addCall($call);
        }
    }
}
