<?php

namespace Domains\Context\BankAccountOperations\Domain\Model\Transaction;

interface ITransactionRepository
{

    public function findAll(): array;

    public function findById(int $id): array;

    public function create(Transaction $transaction): void;

    public function approve(Transaction $transaction): void;
}
