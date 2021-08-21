<?php

namespace Domains\CrossCutting\Domain\Model\ValueObjects;

use Domains\CrossCutting\Domain\Application\Event\Bus\DomainEventBus;
use Domains\CrossCutting\Domain\Application\Event\EventInterface;

abstract class AggregateRoot
{

    /**
     * @var DomainEventBus
     */
    private $domainEventBus;

    public function __construct(DomainEventBus $domainEventBus)
    {
        $this->domainEventBus = $domainEventBus;
    }

    protected function raise(EventInterface $event): void
    {
        $this->domainEventBus->publish($event);
    }

}