<?php

namespace Domains\Context\BankAccountOperations\Application\UseCases\Deposit;

use Domains\Context\BankAccount\Domain\Model\Account\Balance;
use Domains\Context\BankAccountOperations\Domain\Model\Transaction\ITransactionRepository;
use Domains\Context\BankAccountOperations\Domain\Model\Transaction\Transaction;
use Exception;

final class PlaceDepositUseCase implements IPlaceDepositUseCase
{

    public $transaction;

    private $transactionRepository;

    public function __construct(Transaction $transaction, ITransactionRepository $transactionRepository)
    {
        $this->transaction = $transaction;
        $this->transactionRepository = $transactionRepository;
    }

    public function execute(PlaceDepositInput $input): void
    {
        $this->transaction->createFrom($input->accountId, new Balance($input->balance), $input->description, $input->checkPathFile);
        if (!$this->transaction->isValid()) {
            $input->modelState->addErrors($this->transaction->getErrors());
            return;
        }

        try {
            $this->transactionRepository->create($this->transaction);
        } catch (Exception $e) { 
            $input->modelState->addError('Something went wrong while creating a new deposit ' . json_encode($e->getMessage()));
        }
    }
}
