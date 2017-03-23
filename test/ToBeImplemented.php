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
}
