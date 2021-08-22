<?php

namespace Domains\Context\BankAccount\Application\UseCases\Balance;

use Domains\Context\BankAccount\Domain\Model\Account\Account;
use Domains\Context\BankAccount\Domain\Model\Account\Balance;
use Domains\Context\BankAccount\Domain\Model\Account\IAccountRepository;
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

        $transactions = $this->accountRepository->findTransactions($input->accountId);

        if (count($transactions) === 0 || count($transactions[0]['transactions']) === 0) {
            $input->modelState->addError('No result');
            return;
        }

        $totalAmount = 0;

        $account = $transactions[0];
        $transactions = $transactions[0]['transactions'];
        foreach ($transactions as $transaction) {
            $totalAmount += $transaction['balance'];
        }

        $account = $this->account->readFrom($account['id'], $account['customer_id'], $account['account_name'], new Balance($totalAmount));

        if (!$account->isValid()) {
            $input->modelState->addErrors($this->account->getErrors());
            return;
        }

        $this->accountRepository->update($account);
    }
}
