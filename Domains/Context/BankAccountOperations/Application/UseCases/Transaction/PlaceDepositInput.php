<?php

namespace Domains\Context\BankAccountOperations\Application\UseCases\Transaction;

use Domains\CrossCutting\Domain\Application\Services\Common\MessageHandler;

final class PlaceDepositInput
{

    public $accountId;

    public $balance;

    public $description;

    public $checkPathFile;

    public $modelState;

    public function __construct(int $accountId, float $balance, string $description, string $checkPathFile, MessageHandler $modelState)
    {
        $this->accountId = $accountId;
        $this->balance = $balance;
        $this->description = $description;
        $this->checkPathFile = $checkPathFile;
        $this->modelState = $modelState;
    }
}
