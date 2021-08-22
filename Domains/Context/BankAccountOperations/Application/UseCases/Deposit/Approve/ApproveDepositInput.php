<?php

namespace Domains\Context\BankAccountOperations\Application\UseCases\Deposit\Approve;

use Domains\CrossCutting\Domain\Application\Services\Common\MessageHandler;

final class ApproveDepositInput
{

    public $id;

    public $modelState;

    public function __construct(int $id, MessageHandler $modelState)
    {
        $this->id = $id;
        $this->modelState = $modelState;
    }
}
