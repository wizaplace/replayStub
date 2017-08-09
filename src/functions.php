<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace ReplayStub;

function makeMixedValueJsonEncodable($value)
{
    if ($value instanceof \Traversable && !($value instanceof \JsonSerializable)) {
        $value = iterator_to_array($value, true);
    }
    if (is_array($value)) {
        return array_map('\ReplayStub\makeMixedValueJsonEncodable', $value);
    }
    if(is_object($value) && !($value instanceof \JsonSerializable)) {
        if (method_exists($value, '__toString')) {
            $value = (string) $value;
        } else {
            $value = \print_r($value, true);
        }
        return explode("\n", $value); // the explode makes strings with line breaks more readable in a pretty printed JSON
    }
    return $value;
}
