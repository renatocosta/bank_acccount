<?php

namespace Domains\Context\BankAccountOperations\Domain\Model\Transaction;

use Domains\CrossCutting\Domain\Application\Event\AbstractEvent;

class WithdrawalRejected extends AbstractEvent
{

    public $transaction;

    public function __construct(Transaction $transaction)
    {
        parent::__construct();
        $this->transaction = $transaction;
    }
}
