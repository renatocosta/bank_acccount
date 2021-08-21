<?php

namespace Tests\Context\MatchReporting\UnitTests;

use DG\BypassFinals;
use Domains\Context\MatchReporting\Application\UseCases\PlayersKilled\FindPlayersKilledInput;
use Domains\Context\MatchReporting\Application\UseCases\PlayersKilled\FindPlayersKilledUseCase;
use Domains\Context\MatchReporting\Domain\Model\PlayersKilled\Matcher;
use Domains\Context\MatchReporting\Domain\Model\PlayersKilled\PlayersKilledEntity;
use Domains\Context\MatchReporting\Domain\Model\PlayersKilled\State\BasicPlayer;
use Domains\Context\MatchReporting\Domain\Model\PlayersKilled\State\DeadPlayer;
use Domains\Context\MatchReporting\Domain\Model\PlayersKilled\State\KilledPlayer;
use Domains\CrossCutting\Domain\Application\Event\Bus\DomainEventBus;
use Tests\TestCase;

class PlayersKilledTest extends TestCase
{

    public static function setUpBeforeClass(): void
    {
        BypassFinals::enable();
    }

    /**
     * @testWith ["ronaldinho", ""]
     *           ["", "Fagundes"]
     *           ["", ""]
     */
    public function testShouldFailToMatchIfValuesAreMissing(string $whoKilled, string $whoDied)
    {
        $matcher = new Matcher($whoKilled, $whoDied);
        $this->assertFalse($matcher->isValid());
    }

    /**
     * @testWith ["ronaldinho", "ronaldinho"]
     *           ["rivaldo", "RiVAldo"]
     *           ["", ""]
     */
    public function testShouldFailToPlayersAreTheSameName(string $whoKilled, string $whoDied)
    {
        $matcher = new Matcher($whoKilled, $whoDied);
        $this->assertFalse($matcher->isValid());
    }

    public function testShouldFailToCountableRowsIfEntryIsInvalid()
    {
        $basicPlayer = new BasicPlayer(new KilledPlayer(), new DeadPlayer());
        $playersKilled = \Mockery::spy(new PlayersKilledEntity(new DomainEventBus(), $basicPlayer));
        $playersKilled->computeKills(new Matcher('', 'ciclano'));
        $playersKilled->computeKills(new Matcher('', ''));
        $playersKilled->computeKills(new Matcher('garotinho', ''));
        $playersKilled->find();

        $playersKilled->shouldNotHaveReceived('isEligibleToBeAPlayer');

        $this->assertFalse($playersKilled->isValid());
    }

    public function testShouldReturnEmptyPlayersListAtTheBeginning()
    {
        $basicPlayer = new BasicPlayer(new KilledPlayer(), new DeadPlayer());
        $this->assertCount(0, $basicPlayer->getPlayers());
    }

    public function testShouldReturnExpectCountKillsAtTheBeginning()
    {
        $killedPlayer = \Mockery::spy(new KilledPlayer());
        $basicPlayer = new BasicPlayer($killedPlayer, new DeadPlayer());
        $match = new Matcher('fulano', 'ciclano');
        $basicPlayer->killUp($match);
        $killedPlayer->shouldHaveReceived('computeKills')->once();

        $this->assertSame(1, $basicPlayer->getPlayers()['fulano']['kills']);
    }

    public function testShouldReturnExpectCountKillsAfterDeath()
    {
        $killedPlayer = \Mockery::spy(new KilledPlayer());
        $deadPlayer = \Mockery::spy(new DeadPlayer());

        $basicPlayer = new BasicPlayer($killedPlayer, $deadPlayer);
        $match = new Matcher('fulano', 'ciclano');
        $basicPlayer->killUp($match);

        $match = new Matcher('ciclano', 'fulano');
        $basicPlayer->killDown($match);

        $killedPlayer->shouldHaveReceived('computeKills')->once();
        $deadPlayer->shouldHaveReceived('computeKills')->once();

        $this->assertSame(0, $basicPlayer->getPlayers()['fulano']['kills']);
    }

    public function testShouldReturnExpectCountKillsAtTheEnd()
    {
        $killedPlayer = \Mockery::spy(new KilledPlayer());
        $basicPlayer = new BasicPlayer($killedPlayer, new DeadPlayer());
        $match = new Matcher('fulano', 'ciclano');
        $basicPlayer->killUp($match);

        $match = new Matcher('ciclano', 'fulano');
        $basicPlayer->killUp($match);
        $killedPlayer->shouldHaveReceived('computeKills')->twice();

        $this->assertCount(2, $basicPlayer->getPlayers());
    }

    public function testShouldBeAbleToFindPlayersKilledSuccessfully()
    {
        $basicPlayer = new BasicPlayer(new KilledPlayer(), new DeadPlayer());
        $playersKilled = \Mockery::spy(new PlayersKilledEntity(new DomainEventBus(), $basicPlayer));
        $findPlayersKilledUseCase = new FindPlayersKilledUseCase($playersKilled);
        $rows = [['who_killed' => 'fulano', 'who_died' => 'ciclano'], ['who_killed' => 'ciclano', 'who_died' => 'garotinho']];
        $findPlayersKilledUseCase->execute(new FindPlayersKilledInput($rows));
        $playersKilled->shouldHaveReceived('consolidate')->once();
        $playersKilled->shouldHaveReceived('find')->once();
        $this->assertCount(count($rows), $playersKilled->getPlayers());
    }
}
