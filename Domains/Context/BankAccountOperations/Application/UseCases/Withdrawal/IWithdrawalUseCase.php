<?php

namespace Domains\Context\BankAccountOperations\Application\UseCases\Withdrawal;

interface IWithdrawalUseCase
{

    public function execute(WithdrawalInput $input): void;
}
