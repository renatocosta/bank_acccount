<?php

namespace Domains\CrossCutting\Domain\Application\Event\Bus;

use Domains\CrossCutting\Domain\Application\Event\AbstractEvent;
use Domains\CrossCutting\Domain\Application\Event\DomainEventHandler;

final class DomainEventBus
{

    /**
     * @var \SplDoublyLinkedList
     */
    private $eventHandlers;

    public function __construct()
    {
        $this->eventHandlers = new \SplDoublyLinkedList();
    }

    public function __clone()
    {
        throw new \BadMethodCallException('Clone is not supported');
    }

    public function subscribe(DomainEventHandler $aDomainEventHandler): void
    {
        $this->eventHandlers->push($aDomainEventHandler);
    }

    public function publish(AbstractEvent $aDomainEvent): void
    {
        for ($this->eventHandlers->rewind(); $this->eventHandlers->valid(); $this->eventHandlers->next()) {
            $eventHandler = $this->eventHandlers->current();
            if ($eventHandler->isSubscribedTo($aDomainEvent)) {
                $eventHandler->handle($aDomainEvent);
            }

        }

    }

}
