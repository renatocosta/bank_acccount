<?php

namespace Domains\Context\BankAccountOperations\Application\UseCases\Transaction;

interface IPlaceDepositUseCase
{

    public function execute(PlaceDepositInput $input): void;
}
