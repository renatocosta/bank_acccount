<?php

namespace Domains\CrossCutting\Domain\Application\Event;

interface EventInterface
{

    /**
     * @return string
     */
    public function getEventName(): string;

    /**
     * @return \DateTimeImmutable
     */
    public function createdAt(): \DateTimeImmutable;

}