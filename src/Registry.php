<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace ReplayStub;

class Registry
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * @var CallIdSerializer
     */
    private $serializer;

    public function __construct(CallIdSerializer $serializer)
    {
        $this->serializer = $serializer;
    }

    public function addRecord(CallId $id, Result $result)
    {
        $key = $this->serializer->serialize($id);
        $this->data[$key][] = $result;
    }

    public function popRecord(CallId $id): ?Result
    {
        $key = $this->serializer->serialize($id);
        if(!isset($this->data[$key]) || !count($this->data[$key])) {
            return null;
        }

        return array_shift($this->data[$key]);
    }
}
