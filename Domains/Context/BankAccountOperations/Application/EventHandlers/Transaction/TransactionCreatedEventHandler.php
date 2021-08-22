<?php

namespace Domains\Context\BankAccountOperations\Application\EventHandlers\Transaction;

use Domains\Context\BankAccountOperations\Domain\Model\Transaction\DepositPlaced;
use Domains\CrossCutting\Domain\Application\Event\AbstractEvent;
use Domains\CrossCutting\Domain\Application\Event\DomainEventHandler;
use Illuminate\Support\Facades\Log;

final class TransactionCreatedEventHandler implements DomainEventHandler
{

    public function handle(AbstractEvent $domainEvent): void
    {
        Log::info(__CLASS__);
    }

    public function isSubscribedTo(AbstractEvent $domainEvent): bool
    {
        return $domainEvent instanceof DepositPlaced;
    }
}
