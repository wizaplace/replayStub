<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace ReplayStub\Test;

/**
 * Simple interface only here for testing purposes
 */
interface ToBeImplemented {
    public function get4();

    public function extra() : array;

    public function throwingMethod();

    public function idem(string $str) : string;

    public function increment();

    public static function staticFunc() : bool;

    public function me2() : self;

    public function __toString() : string;

    public function withDefault($param = 42, $param2 = null, $param3 = false, $param4 = ToBeDecorated::SOME_CONST, $param5 = []) : int;
}
