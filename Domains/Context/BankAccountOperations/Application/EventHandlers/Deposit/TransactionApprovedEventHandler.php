<?php

namespace Domains\Context\BankAccountOperations\Application\EventHandlers\Deposit;

use Domains\Context\BankAccountOperations\Domain\Model\Transaction\DepositApproved;
use Domains\Context\BankAccountOperations\Domain\Model\Transaction\DepositPlaced;
use Domains\CrossCutting\Domain\Application\Event\AbstractEvent;
use Domains\CrossCutting\Domain\Application\Event\DomainEventHandler;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

final class TransactionApprovedEventHandler implements DomainEventHandler
{

    public function handle(AbstractEvent $domainEvent): void
    {
        //Upstream to Bank Account domain in order to recalculate its balance
        $request = Request::create('api/bankaccount/balance/{account_id}/operation/{operation}/amount/{amount}', 'PATCH', ['account_id' => $domainEvent->transaction->getAccountId(), 'operation' => 'Deposit', 'amount' => $domainEvent->transaction->getBalance()->value]);
        Request::replace($request->input());
        $response = Route::dispatch($request);
    }

    public function isSubscribedTo(AbstractEvent $domainEvent): bool
    {
        return $domainEvent instanceof DepositApproved;
    }
}
