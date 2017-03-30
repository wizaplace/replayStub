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
}
