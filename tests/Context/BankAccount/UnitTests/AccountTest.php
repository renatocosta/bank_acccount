<?php

namespace Tests\Context\BankAccount\UnitTests;

use DG\BypassFinals;
use Domains\Context\BankAccount\Application\EventHandlers\Account\AccountCreatedEventHandler;
use Domains\Context\BankAccount\Application\EventHandlers\Account\AccountRejectedEventHandler;
use Domains\Context\BankAccount\Application\UseCases\Account\CreateAccountInput;
use Domains\Context\BankAccount\Application\UseCases\Account\CreateAccountUseCase;
use Domains\Context\BankAccount\Application\UseCases\Balance\RecalculateBalanceInput;
use Domains\Context\BankAccount\Application\UseCases\Balance\RecalculateBalanceUseCase;
use Domains\Context\BankAccount\Domain\Model\Account\AccountEntity;
use Domains\Context\BankAccount\Infrastructure\Framework\DataAccess\Repositories\AccountRepository;
use Domains\Context\BankAccount\Infrastructure\Framework\Entities\AccountModel;
use Domains\CrossCutting\Domain\Application\Event\Bus\DomainEventBus;
use Domains\CrossCutting\Domain\Application\Services\Common\MessageHandler;
use Tests\TestCase;

class AccountTest extends TestCase
{

    public static function setUpBeforeClass(): void
    {
        BypassFinals::enable();
    }

    public function testShouldFailToAccountBalanceIfEntryIsInvalid()
    {
        $domainEventBus = new DomainEventBus();
        $domainEventBus->subscribe(new AccountCreatedEventHandler());
        $domainEventBus->subscribe(new AccountRejectedEventHandler());
        $account = \Mockery::spy(new AccountEntity($domainEventBus));
        $accountModel = new AccountModel();
        $accountRepository = \Mockery::spy(new AccountRepository($accountModel));
        $createAccountUseCase = new CreateAccountUseCase($account, $accountRepository);
        $input = new CreateAccountInput(0, 'xyz', new MessageHandler());
        $createAccountUseCase->execute($input);
        $accountRepository->shouldNotHaveReceived('create');

        $this->assertFalse($account->isValid());
    }

    public function testShouldFailToAccountBalanceIfNoSuchAccount()
    {
        $domainEventBus = new DomainEventBus();
        $domainEventBus->subscribe(new AccountCreatedEventHandler());
        $domainEventBus->subscribe(new AccountRejectedEventHandler());
        $account = new AccountEntity($domainEventBus);
        $accountModel = new AccountModel();
        $accountRepository = new AccountRepository($accountModel);
        $recalculateBalanceUseCase = new RecalculateBalanceUseCase($account, $accountRepository);
        $recalculateBalanceUseCase->execute(new RecalculateBalanceInput(611, new MessageHandler()));
        $this->assertTrue($account->isValid());
    }
}
