<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace ReplayStub;

require_once(__DIR__.'/functions.php');

class Result implements \JsonSerializable, \Serializable
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @var \Throwable|null
     */
    private $exception;

    public function __construct($value, ?\Throwable $exception = null)
    {
        $this->init($value, $exception);
    }

    /**
     * @return mixed
     * @throws \Throwable
     */
    public function produce()
    {
        if (!is_null($this->exception)) {
            throw $this->exception;
        }

        return $this->value;
    }

    public function getException() : ?\Throwable
    {
        return $this->exception;
    }

    public function serialize(): string
    {
        return \serialize([
            'value' => $this->value,
            'exception' => $this->exception,
        ]);
    }

    public function unserialize($serialized): void
    {
        $data = \unserialize($serialized, [
            'allowed_classes' => true,
        ]);

        $this->init($data['value'], $data['exception'] ?? null);
    }

    public function jsonSerialize(): array
    {
        return [
            'value' => makeMixedValueJsonEncodable($this->value),
            'exception' => makeMixedValueJsonEncodable($this->exception),
        ];
    }

    private function init($value, ?\Throwable $exception = null): void
    {
        $this->value = $value;
        $this->exception = $exception;
    }
}
