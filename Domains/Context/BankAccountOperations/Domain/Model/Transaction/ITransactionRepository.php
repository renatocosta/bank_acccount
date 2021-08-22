<?php

namespace Domains\Context\BankAccountOperations\Domain\Model\Transaction;

interface ITransactionRepository
{

    public function findAll(): array;

    public function create(Transaction $transaction): void;

}
