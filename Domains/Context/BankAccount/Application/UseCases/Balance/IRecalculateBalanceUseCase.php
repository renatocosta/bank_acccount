<?php

namespace Domains\Context\BankAccount\Application\UseCases\Balance;

interface IRecalculateBalanceUseCase
{

    public function execute(RecalculateBalanceInput $input): void;
}
