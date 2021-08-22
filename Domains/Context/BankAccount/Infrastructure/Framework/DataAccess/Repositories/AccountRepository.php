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
        $model = $this->accountModel->find($account->getId());
        $model->current_balance = $account->getBalance()->value;
        $model->save();
    }

    public function create(Account $account): void
    {
        $this->accountModel->fill(['customer_id' => $account->getCustomerId(), 'account_name' => $account->getAccountName(), 'current_balance' => $account->getBalance()->value]);
        $this->accountModel->save();
        $account->setId($this->accountModel->id);
    }

    public function findById(int $id): array
    {
        $result = $this->accountModel->find($id);
        if ($result === null) return [];

        return $result->toArray();
    }

    public function findTransactions(int $accountId): array
    {
        return $this->accountModel
            ->with(['transactions' => function ($query) {
                $query->select('account_id', 'balance', 'description', 'check_path_file', 'approved', 'created_at');
            }])
            ->has('transactions')
            ->where('id', $accountId)
            ->get()
            ->toArray();
    }
}
