<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace ReplayStub;

abstract class Utils
{
    public static function jsonEncodableFromMixedValue($value)
    {
        if(is_scalar($value) || is_null($value)) {
            return $value;
        }

        if(is_array($value)) {
            return array_map('\ReplayStub\Utils::jsonEncodableFromMixedValue', $value);
        }

        $result = explode("\n", \print_r($value, true));
        if (count($result) === 1) {
            return reset($result);
        }
        return $result;
    }
}
