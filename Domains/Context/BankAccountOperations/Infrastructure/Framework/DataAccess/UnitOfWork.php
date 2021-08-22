<?php

namespace Domains\Context\BankAccountOperations\Infrastructure\Framework\DataAccess;

use Domains\CrossCutting\Domain\Infrastructure\Transaction\IUnitOfWork;

final class UnitOfWork implements IUnitOfWork
{

    public function beginTransaction(): void
    {
        \DB::beginTransaction();
    }

    public function commit(): void
    {
        \DB::commit();
    }

    public function rollback(): void
    {
        \DB::rollBack();
    }

}