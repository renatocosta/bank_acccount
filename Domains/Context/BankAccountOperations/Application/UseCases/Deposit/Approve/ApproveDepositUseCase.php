<?php

namespace Domains\Context\BankAccountOperations\Application\UseCases\Deposit\Approve;

use Domains\Context\BankAccount\Domain\Model\Account\Balance;
use Domains\Context\BankAccountOperations\Domain\Model\Transaction\ITransactionRepository;
use Domains\Context\BankAccountOperations\Domain\Model\Transaction\Transaction;
use Exception;

final class ApproveDepositUseCase implements IApproveDepositUseCase
{

    public $transaction;

    private $transactionRepository;

    public function __construct(Transaction $transaction, ITransactionRepository $transactionRepository)
    {
        $this->transaction = $transaction;
        $this->transactionRepository = $transactionRepository;
    }

    public function execute(ApproveDepositInput $input): void
    {

        $transaction = $this->transactionRepository->findById($input->id);
        if (count($transaction) === 0) {
            $input->modelState->addError('No result');
            return;
        }

        $transaction = $this->transaction->readFrom($transaction['id'], $transaction['account_id'], new Balance($transaction['balance']), $transaction['description'], $transaction['check_path_file'], true);

        if (!$transaction->isValid()) {
            $input->modelState->addErrors($this->transaction->getErrors());
            return;
        }

        $this->transactionRepository->approve($transaction);
    }
}
