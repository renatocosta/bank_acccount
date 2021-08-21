<?php

namespace Domains\Context\BankAccount\Domain\Model\Account;

use Domains\CrossCutting\Domain\Application\Event\AbstractEvent;

class AccountRejected extends AbstractEvent
{

    public $account;

    public function __construct(Account $account)
    {
        parent::__construct();
        $this->account = $account;
    }
}
