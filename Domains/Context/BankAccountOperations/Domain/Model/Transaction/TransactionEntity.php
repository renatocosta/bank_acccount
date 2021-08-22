<?php

namespace Domains\Context\BankAccountOperations\Domain\Model\Transaction;

use Assert\Assert;
use Assert\AssertionFailedException;
use Domains\Context\BankAccount\Domain\Model\Account\Balance;
use Domains\CrossCutting\Domain\Application\Event\Bus\DomainEventBus;
use Domains\CrossCutting\Domain\Model\ValueObjects\AggregateRoot;
use Illuminate\Support\Facades\Log;

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

    public function createFrom(int $accountId, Balance $balance, string $description, string $checkPathFile, bool $approved = false): Transaction
    {

        $this->accountId = $accountId;
        $this->balance = $balance;
        $this->description = $description;
        $this->checkPathFile = $checkPathFile;
        $this->approved = $approved;

        if ($this->isEligible()) {
            $this->raise(new DepositPlaced($this));
        } else {
            $this->raise(new DepositRejected($this));
        }

        return $this;
    }

    public function readFrom(int $id, int $accountId, Balance $balance, string $description, string $checkPathFile, bool $approved): Transaction
    {
        $this->id = $id;
        $this->accountId = $accountId;
        $this->balance = $balance;
        $this->description = $description;
        $this->checkPathFile = $checkPathFile;
        $this->approved = $approved;

        if ($this->isEligible()) {
            $this->raise(new DepositApproved($this));
        } else {
            $this->raise(new ApproveDepositRejected($this));
        }
        return $this;
    }

    public function isEligible(): bool
    {
        try {
            Assert::that($this->balance->value, 'BALANCE_MUST_BE_GREATER_THAN_ZERO')->greaterThan(0);
        } catch (AssertionFailedException $e) {
            $this->errors[] = $e->getMessage();
            return false;
        }

        return true;
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
