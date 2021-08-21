<?php

namespace Domains\Context\BankAccount\Application\UseCases\Account;

interface ICreateAccountUseCase
{

    public function execute(CreateAccountInput $input): void;
}
