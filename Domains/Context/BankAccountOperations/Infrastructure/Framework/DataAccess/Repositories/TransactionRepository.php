<?php

namespace Domains\Context\BankAccountOperations\Infrastructure\Framework\DataAccess\Repositories;

use Domains\Context\BankAccountOperations\Domain\Model\Transaction\ITransactionRepository;
use Domains\Context\BankAccountOperations\Domain\Model\Transaction\Transaction;
use Domains\Context\BankAccountOperations\Infrastructure\Framework\Entities\TransactionsModel;

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

}
