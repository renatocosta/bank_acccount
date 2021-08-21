<?php

namespace Domains\Context\BankAccount\Application\UseCases\Account;

use Domains\Context\BankAccount\Domain\Model\Account\Account;
use Domains\Context\BankAccount\Domain\Model\Account\Balance;
use Domains\Context\BankAccount\Domain\Model\Account\IAccountRepository;
use Illuminate\Support\Facades\Log;
use Exception;

final class CreateAccountUseCase implements ICreateAccountUseCase
{

    public $account;

    private $accountRepository;

    public function __construct(Account $account, IAccountRepository $accountRepository)
    {
        $this->account = $account;
        $this->accountRepository = $accountRepository;
    }

    public function execute(CreateAccountInput $input): void
    {
        $this->account->createFrom($input->customerId, $input->accountName, new Balance($input->currentBalance));
        if (!$this->account->isValid()) {
            $input->modelState->addErrors($this->account->getErrors());
            return;
        }

        $this->accountRepository->create($this->account);
    }
}
