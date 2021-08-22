<?php

namespace Domains\Context\BankAccountOperations\Application\UseCases\Withdrawal;

use Domains\Context\BankAccount\Domain\Model\Account\Balance;
use Domains\Context\BankAccountOperations\Domain\Model\Transaction\ITransactionRepository;
use Domains\Context\BankAccountOperations\Domain\Model\Transaction\Transaction;
use Exception;

final class WithdrawalUseCase implements IWithdrawalUseCase
{

    public $transaction;

    private $transactionRepository;

    public function __construct(Transaction $transaction, ITransactionRepository $transactionRepository)
    {
        $this->transaction = $transaction;
        $this->transactionRepository = $transactionRepository;
    }

    public function execute(WithdrawalInput $input): void
    {

        $transaction = $this->transaction->withdrawal($input->id, new Balance($input->balance), 'withdrawal statement', '-', true);

        if (!$transaction->isValid()) {
            $input->modelState->addErrors($this->transaction->getErrors());
            return;
        }

        $this->transactionRepository->withdrawal($transaction);
    }
}
