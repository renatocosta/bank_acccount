<?php

namespace Domains\CrossCutting\Domain\Application\Event;

use Illuminate\Support\Facades\Log;

abstract class AbstractEvent implements EventInterface
{

    /**
     * @var string
     */
    private $eventName;

    /**
     * @var \DateTimeImmutable
     */
    private $createdAt;

    public function __construct()
    {
        $this->setEventName();
        $this->createdAt = new \DateTimeImmutable();
        $this->showEventDetails();
    }

    private function setEventName(): void
    {
        $path = explode('\\', get_class($this));
        $this->eventName = array_pop($path);
    }

    public function getEventName(): string
    {
        return $this->eventName;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function showEventDetails(): void
    {
        Log::info(sprintf('Domain event %s runned at %s', $this->getEventName(), $this->createdAt()->format('Y-m-d H:i:s.u')));
    }
}
