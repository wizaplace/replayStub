<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace RePHPlay;

class Registry
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    public function addRecord(Id $id, Result $result)
    {
        $key = $this->serializer->serialize($id);
        $this->data[$key][] = $result;
    }

    public function popRecord(Id $id) : ?Result
    {
        $key = $this->serializer->serialize($id);
        if(!isset($this->data[$key]) || !count($this->data[$key])) {
            return null;
        }

        return array_pop($this->data[$key]);
    }
}
