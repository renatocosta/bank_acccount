<?php

namespace Domains\Context\BankAccount\Domain\Model\Account;

use Assert\Assert;
use Assert\AssertionFailedException;
use Domains\Context\BankAccount\Domain\Model\Account\Account;
use Domains\Context\BankAccount\Domain\Model\Account\AccountCreated;
use Domains\Context\BankAccount\Domain\Model\Account\AccountRejected;
use Domains\Context\BankAccount\Domain\Model\Account\Balance;
use Domains\CrossCutting\Domain\Application\Event\Bus\DomainEventBus;
use Domains\CrossCutting\Domain\Model\ValueObjects\AggregateRoot;

final class AccountEntity extends AggregateRoot implements Account
{

    private $id;

    private $customerId;

    private $accountName;

    private $balance;

    private $errors = [];

    public function __construct(DomainEventBus $domainEventBus)
    {
        parent::__construct($domainEventBus);
    }

    public function createFrom(int $customerId, string $accountName, Balance $balance): Account
    {

        $this->customerId = $customerId;
        $this->accountName = $accountName;
        $this->balance = $balance;

        if ($this->isEligible()) {
            $this->raise(new AccountCreated($this));
        } else {
            $this->raise(new AccountRejected($this));
        }

        return $this;
    }

    public function isEligible(): bool
    {
        try {
            Assert::that($this->customerId, 'CUSTOMER_ID_CAN_NOT_BE_ZERO_OR_NEGATIVE')->greaterThan(0);
        } catch (AssertionFailedException $e) {
            $this->errors[] = $e->getMessage();
            return false;
        }

        return true;
    }

    public function readFrom(int $id, int $customerId, string $accountName, Balance $balance): Account
    {
        $this->id = $id;
        $this->customerId = $customerId;
        $this->accountName = $accountName;
        $this->balance = $balance;

        $this->isEligible();
        return $this;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function getAccountName(): string
    {
        return $this->accountName;
    }

    public function getBalance(): Balance
    {
        return $this->balance;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function isValid(): bool
    {
        return count($this->errors) === 0;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function __toString(): string
    {
        return sprintf('Customer Id %s Customer name %s Balance %f', $this->customerId, $this->accountName, $this->balance->value);
    }
}
