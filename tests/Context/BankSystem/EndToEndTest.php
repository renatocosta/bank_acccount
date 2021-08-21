<?php

namespace Tests\Context\Quake3ArenaLogging\UnitTests;

use Domains\Context\LogHandler\Application\EventHandlers\HumanLogFile\HumanLogFileCreatedForDeathCausesEventHandler;
use Domains\Context\LogHandler\Application\EventHandlers\HumanLogFile\HumanLogFileCreatedForPlayersKilledEventHandler;
use Domains\Context\LogHandler\Application\EventHandlers\HumanLogFile\HumanLogFileRejectedEventHandler;
use Domains\Context\LogHandler\Application\EventHandlers\LogFile\LogFileRejectedEventHandler;
use Domains\Context\LogHandler\Application\EventHandlers\LogFile\LogFileSelectedEventHandler;
use Domains\Context\LogHandler\Application\UseCases\Factories\QuakeDataCollector;
use Domains\Context\LogHandler\Application\UseCases\Factories\QuakeDataCollectorFactory;
use Domains\CrossCutting\Domain\Application\Event\Bus\DomainEventBus;
use Tests\TestCase;

class EndToEndTest extends TestCase
{

    public function testShouldBeAbleToCreateLogFileSuccessfully()
    {
        $playersKilledCollector = new QuakeDataCollector(new DomainEventBus());
        $playersKilledCollector->attachEventHandler(new LogFileSelectedEventHandler($playersKilledCollector->getCreateHumanLogFileUseCase()));
        $playersKilledCollector->attachEventHandler(new LogFileRejectedEventHandler());
        $playersKilledCollector->attachEventHandler(new HumanLogFileRejectedEventHandler());

        $humanLogFile = $playersKilledCollector->getHumanLogFile();
        $this->assertTrue($humanLogFile->isValid());
        return $playersKilledCollector;
    }


    /**
     * @depends testShouldBeAbleToCreateLogFileSuccessfully
     * 
     */
    public function testShouldBeAbleToMatchDeathCausesSuccessfully(QuakeDataCollectorFactory $quakeDataCollector)
    {
        $quakeDataCollector->attachEventHandler(new HumanLogFileCreatedForDeathCausesEventHandler($quakeDataCollector->getFindDeathCausesUseCase()));
        $quakeDataCollector->dispatch();
        $this->assertTrue($quakeDataCollector->getDeathCauses()->isValid());
    }

    /**
     * @depends testShouldBeAbleToCreateLogFileSuccessfully
     */
    public function testShouldBeAbleToMatchPlayersKilledSuccessfully(QuakeDataCollectorFactory $quakeDataCollector)
    {
        $quakeDataCollector->attachEventHandler(new HumanLogFileCreatedForPlayersKilledEventHandler($quakeDataCollector->getFindPlayersKilledUseCase()));
        $quakeDataCollector->dispatch();
        $this->assertTrue($quakeDataCollector->getPlayersKilled()->isValid());
    }

}