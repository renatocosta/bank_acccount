<?php

namespace Domains\Context\BankAccountOperations\Application\UseCases\Deposit\Approve;

interface IApproveDepositUseCase
{

    public function execute(ApproveDepositInput $input): void;
}
