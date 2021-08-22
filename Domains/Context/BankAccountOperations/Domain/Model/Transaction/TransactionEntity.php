<?php

namespace Domains\Context\BankAccountOperations\Domain\Model\Transaction;

use Assert\Assert;
use Assert\AssertionFailedException;
use Domains\Context\BankAccount\Domain\Model\Account\Balance;
use Domains\CrossCutting\Domain\Application\Event\Bus\DomainEventBus;
use Domains\CrossCutting\Domain\Model\ValueObjects\AggregateRoot;

final class TransactionEntity extends AggregateRoot implements Transaction
{

    private $id;

    private $accountId;

    private $balance;

    private $description;

    private $checkPathFile;

    private $approved;

    private $errors = [];

    public function __construct(DomainEventBus $domainEventBus)
    {
        parent::__construct($domainEventBus);
    }

    public function createFrom(int $accountId, Balance $balance, string $description, string $checkPathFile): Transaction
    {

        $this->accountId = $accountId;
        $this->balance = $balance;
        $this->description = $description;
        $this->checkPathFile = $checkPathFile;

        try {
            Assert::that($this->balance->value, 'CUSTOMER_ID_CAN_NOT_BE_ZERO_OR_NEGATIVE')->greaterThan(0);
            $this->raise(new DepositPlaced($this));
        } catch (AssertionFailedException $e) {
            $this->errors[] = $e->getMessage();
            $this->raise(new DepositRejected($this));
        }

        return $this;
    }

    public function getAccountId(): int
    {
        return $this->accountId;
    }

    public function getBalance(): Balance
    {
        return $this->balance;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getCheckPathFile(): string
    {
        return $this->checkPathFile;
    }

    public function getApproved(): bool
    {
        return $this->approved;
    }

    public function setId(int $id): void
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
}
