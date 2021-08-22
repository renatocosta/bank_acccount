<?php

namespace Domains\Context\BankAccountOperations\Application\EventHandlers\Deposit;

use Domains\Context\BankAccountOperations\Domain\Model\Transaction\DepositRejected;
use Domains\CrossCutting\Domain\Application\Event\AbstractEvent;
use Domains\CrossCutting\Domain\Application\Event\DomainEventHandler;
use Illuminate\Support\Facades\Log;

final class TransactionRejectedEventHandler implements DomainEventHandler
{

    public function handle(AbstractEvent $domainEvent): void
    {
        Log::info(__CLASS__);
    }

    public function isSubscribedTo(AbstractEvent $domainEvent): bool
    {
        return $domainEvent instanceof DepositRejected;
    }
}
