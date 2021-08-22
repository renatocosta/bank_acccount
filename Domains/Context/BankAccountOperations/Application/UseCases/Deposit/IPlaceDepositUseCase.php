<?php

namespace Domains\Context\BankAccountOperations\Application\UseCases\Deposit;

interface IPlaceDepositUseCase
{

    public function execute(PlaceDepositInput $input): void;
}
