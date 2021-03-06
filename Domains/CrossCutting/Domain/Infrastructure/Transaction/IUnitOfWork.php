<?php

namespace Domains\CrossCutting\Domain\Infrastructure\Transaction;

/**
 * The unit of work implementation manages in-memory database CRUD operations on entities as one transaction
 */
interface IUnitOfWork
{

    public function beginTransaction(): void;

    public function commit(): void;

    public function rollback(): void;

}