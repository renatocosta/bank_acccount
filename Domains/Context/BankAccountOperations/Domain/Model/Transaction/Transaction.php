<?php

namespace Domains\Context\BankAccountOperations\Domain\Model\Transaction;

use Domains\Context\BankAccount\Domain\Model\Account\Balance;
use Domains\CrossCutting\Domain\Model\Common\Validatable;

interface Transaction extends Validatable
{

    public function createFrom(int $accountId, Balance $balance, string $description, string $checkPathFile): Transaction;

    public function getAccountId(): int;

    public function getBalance(): Balance;

    public function getDescription(): string;

    public function getCheckPathFile(): string;

    public function getApproved(): bool;

    public function setId(int $id): void;

    public function getId(): int;


}
