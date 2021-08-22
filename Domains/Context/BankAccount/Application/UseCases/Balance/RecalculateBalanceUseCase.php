<?php

namespace Domains\Context\BankAccount\Application\UseCases\Balance;

use Domains\Context\BankAccount\Domain\Model\Account\Account;
use Domains\Context\BankAccount\Domain\Model\Account\Balance;
use Domains\Context\BankAccount\Domain\Model\Account\IAccountRepository;
use Domains\Context\BankAccount\Domain\Model\Account\TransactionInfo;
use Domains\Context\BankAccount\Domain\Model\Account\TransactionOperation;
use Illuminate\Support\Facades\Log;
use Exception;

final class RecalculateBalanceUseCase implements IRecalculateBalanceUseCase
{

    public $account;

    private $accountRepository;

    public function __construct(Account $account, IAccountRepository $accountRepository)
    {
        $this->account = $account;
        $this->accountRepository = $accountRepository;
    }

    public function execute(RecalculateBalanceInput $input): void
    {

        $account = $this->accountRepository->findById($input->accountId);

        if (count($account) === 0) {
            $input->modelState->addError('No result');
            return;
        }

        $operation = new TransactionOperation($input->operation);

        if ($operation == TransactionInfo::DEPOSIT) {
            $totalAmount = $account['current_balance'] + $input->newAmount;
        } else {
            $totalAmount = $account['current_balance'] - $input->newAmount;
        }

        $account = $this->account->readFrom($account['id'], $account['customer_id'], $account['account_name'], new Balance($totalAmount));

        if (!$account->isValid()) {
            $input->modelState->addErrors($this->account->getErrors());
            return;
        }

        $this->accountRepository->update($account);
    }
}
