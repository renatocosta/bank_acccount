<?php

namespace Domains\Context\BankAccount\Infrastructure\Framework\DataAccess\Repositories;

use Domains\Context\BankAccount\Domain\Model\Account\Account;
use Domains\Context\BankAccount\Domain\Model\Account\IAccountRepository;
use Domains\Context\BankAccount\Infrastructure\Framework\Entities\AccountModel;

final class AccountRepository implements IAccountRepository
{

    private $accountModel;

    private const TOTAL_PER_PAGE = 20;

    public function __construct(AccountModel $accountModel)
    {
        $this->accountModel = $accountModel;
    }

    public function findAll(): array
    {
        return  [];
    }

    public function update(Account $account): void
    {
        // Not implemented yet
    }

    public function create(Account $account): void
    {
        $this->accountModel->fill(['customer_id' => $account->getCustomerId(), 'account_name' => $account->getAccountName(), 'current_balance' => $account->getBalance()->value]);
        $this->accountModel->save();
        $account->setId($this->accountModel->id);
    }

    public function findById(int $id): array
    {
        return [];
    }
}
