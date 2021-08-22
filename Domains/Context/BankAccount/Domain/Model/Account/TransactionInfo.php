<?php

namespace Domains\Context\BankAccount\Domain\Model\Account;

final class TransactionInfo
{

    public const DEPOSIT = 'Deposit';

    public const WITHDRAWAL = 'Withdrawal';

    public const BANKING_OPERATIONS = [self::DEPOSIT, self::WITHDRAWAL];
}
