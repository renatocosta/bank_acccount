<?php

namespace Domains\Context\BankAccountOperations\Infrastructure\Framework\DataAccess\Repositories;

use Domains\Context\BankAccount\Domain\Model\Account\Balance;
use Domains\Context\BankAccountOperations\Domain\Model\Transaction\ITransactionRepository;
use Domains\Context\BankAccountOperations\Domain\Model\Transaction\Transaction;
use Domains\Context\BankAccountOperations\Infrastructure\Framework\Entities\TransactionsModel;
use Illuminate\Support\Facades\Log;

final class TransactionRepository implements ITransactionRepository
{

    private $transactionModel;

    private const TOTAL_PER_PAGE = 20;

    public function __construct(TransactionsModel $transactionModel)
    {
        $this->transactionModel = $transactionModel;
    }

    public function findAll(): array
    {
        return  [];
    }

    public function create(Transaction $transaction): void
    {
        $this->transactionModel->fill(['account_id' => $transaction->getAccountId(), 'balance' => $transaction->getBalance()->value, 'description' => $transaction->getDescription(), 'check_path_file' => $transaction->getCheckPathFile()]);
        $this->transactionModel->save();
        $transaction->setId($this->transactionModel->id);
    }

    public function findById(int $id): array
    {

        $result = $this->transactionModel
            ->find($id);

        if ($result === null) return [];

        return $result->toArray();
    }

    public function approve(Transaction $transaction): void
    {
        $model = $this->transactionModel->find($transaction->getId());
        $model->approved = $transaction->getApproved();
        $model->save();
    }

    public function withdrawal(Transaction $transaction): void
    {
        $this->transactionModel->fill(['account_id' => $transaction->getAccountId(), 'balance' => $transaction->getBalance()->value, 'description' => $transaction->getDescription(), 'check_path_file' => $transaction->getCheckPathFile(), 'approved' => $transaction->getApproved()]);
        $this->transactionModel->save();
        $transaction->setId($this->transactionModel->id);
    }
}
