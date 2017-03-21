<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace RePHPlay\Test;

/**
 * Simple class only here for testing purposes
 */
class ToBeDecorated implements ToBeImplemented {
    public function get4() {
        return 4;
    }

    public function extra() : array {
        return func_get_args();
    }

    public function throwingMethod() {
        throw new ExpectedException();
    }

    public function idem(string $str) : string {
        return $str;
    }

    public function increment() {
        static $i = 0;
        return $i++;
    }

    public static function staticFunc() : bool {
        return true;
    }

    public function me() : self {
        return $this;
    }

    public function me2() : ToBeImplemented {
        return $this;
    }

    public function __toString() : string
    {
        return 'stringified';
    }
};
