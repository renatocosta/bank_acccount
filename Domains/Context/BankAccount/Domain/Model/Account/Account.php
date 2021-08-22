<?php

namespace Domains\Context\BankAccount\Domain\Model\Account;

use Domains\CrossCutting\Domain\Model\Common\Validatable;

interface Account extends Validatable
{

    public function createFrom(int $customerId, string $accountName, Balance $balance): Account;

    public function readFrom(int $id, int $customerId, string $accountName, Balance $balance): Account;

    public function isEligible(): bool;

    public function getCustomerId(): int;

    public function getAccountName(): string;

    public function getBalance(): Balance;

    public function setId(int $id);

    public function getId(): int;
}
