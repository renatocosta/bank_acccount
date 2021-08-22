<?php

namespace Domains\Context\BankAccountOperations\Application\EventHandlers\Withdrawal;

use Domains\Context\BankAccountOperations\Domain\Model\Transaction\WithdrawalPlaced;
use Domains\CrossCutting\Domain\Application\Event\AbstractEvent;
use Domains\CrossCutting\Domain\Application\Event\DomainEventHandler;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

final class WithdrawalPlacedEventHandler implements DomainEventHandler
{

    public function handle(AbstractEvent $domainEvent): void
    {
        //Upstream to Bank Account domain in order to recalculate its balance
        $request = Request::create('api/bankaccount/balance/{account_id}/operation/{operation}/amount/{amount}', 'PATCH', ['account_id' => $domainEvent->transaction->getAccountId(), 'operation' => 'Withdrawal', 'amount' => $domainEvent->transaction->getBalance()->value]);
        Request::replace($request->input());
        $response = Route::dispatch($request);
    }

    public function isSubscribedTo(AbstractEvent $domainEvent): bool
    {
        return $domainEvent instanceof WithdrawalPlaced;
    }
}
