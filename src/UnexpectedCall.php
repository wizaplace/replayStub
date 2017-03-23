<?php
/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     Proprietary
 */
declare(strict_types = 1);

namespace ReplayStub;

class UnexpectedCall extends \Exception
{
    /**
     * @var CallId
     */
    private $id;

    public function __construct(CallId $id)
    {
        $this->id = $id;
        $message = 'Unexpected call to a ReplayStub mock. The registry does not contain a result for this call:';
        $message .= " {$id->getClass()}::{$id->getMethod()}(" . implode(', ', array_map([$this, 'arg2String'], $id->getArgs())) . ")";
        if (!is_null($id->getInstanceId())) {
            $message .= " [instanceId: {$id->getInstanceId()}]";
        }
        parent::__construct($message);
    }

    private static function arg2String($arg): string
    {
        return var_export($arg, true);
    }

    /**
     * @return CallId
     */
    public function getCallId(): CallId
    {
        return $this->id;
    }
}
