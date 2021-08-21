<?php

namespace Tests\Context\MatchReporting\UnitTests;

use Domains\Context\MatchReporting\Application\UseCases\DeathCauses\FindDeathCausesInput;
use Domains\Context\MatchReporting\Application\UseCases\DeathCauses\FindDeathCausesUseCase;
use Domains\Context\MatchReporting\Domain\Model\DeathCauses\DeathCauseInfo;
use Domains\Context\MatchReporting\Domain\Model\DeathCauses\DeathCausesEntity;
use Domains\Context\MatchReporting\Domain\Model\DeathCauses\Matcher;
use Domains\CrossCutting\Domain\Application\Event\Bus\DomainEventBus;
use Tests\TestCase;

class DeathCausesTest extends TestCase
{
    /**
     * @testWith ["NO SUCH MOD"]
     *           ["  SSS"]
     *           [" "]
     */
    public function testShouldFailToListCausesIfEntryIsInvalid(string $invalidCause)
    {
        $matcher = new Matcher($invalidCause);
        $this->assertFalse($matcher->isValid());
    }

    public function testShouldFailToCountableRowsIfEntryIsInvalid()
    {
        $deathCauses = \Mockery::spy(new DeathCausesEntity(new DomainEventBus()));
        $deathCauses->computeCause(new Matcher('No such means'));
        $deathCauses->computeCause(new Matcher(DeathCauseInfo::MOD_SHOTGUN));
        $deathCauses->computeCause(new Matcher(DeathCauseInfo::MOD_CHAINGUN));
        $deathCauses->find();

        $deathCauses->shouldHaveReceived('find');

        $this->assertFalse($deathCauses->isValid());
    }

    public function testShouldBeAbleToFindDeathCausesSuccessfully()
    {
        $deathCauses = new DeathCausesEntity(new DomainEventBus());
        $findDeathCausesUseCase = new FindDeathCausesUseCase($deathCauses);
        $means = [['means_of_death' => DeathCauseInfo::MOD_FALLING], ['means_of_death' => DeathCauseInfo::MOD_SHOTGUN]];
        $findDeathCausesUseCase->execute(new FindDeathCausesInput($means));
        $this->assertCount(count($means), $deathCauses->getCauses());
    }
}
