<?php

namespace Tests\Context\LogHandler\UnitTests;

use Domains\Context\LogHandler\Application\Services\HumanRowMapper;
use Domains\Context\LogHandler\Application\UseCases\HumanLogFile\CreateHumanLogFileInput;
use Domains\Context\LogHandler\Application\UseCases\HumanLogFile\CreateHumanLogFileUseCase;
use Domains\Context\LogHandler\Domain\Model\HumanLogFile\HumanLogFileEntity;
use Domains\Context\LogHandler\Domain\Model\HumanLogFile\HumanLogFileRow;
use Domains\CrossCutting\Domain\Application\Event\Bus\DomainEventBus;
use Generator;
use Tests\TestCase;

class HumanLogFileTest extends TestCase
{

    /**
     * @testWith ["ronaldinho", "", "xyz sss"]
     *           ["", "Fagundes", "wsss"]
     *           ["rivelino", "silvio", ""]
     */
    public function testShouldFailToRowFileIfValuesAreMissing(string $whoKilled, string $whoDied, $means)
    {
        $humanLogFileRow = new HumanLogFileRow($whoKilled, $whoDied, $means);
        $humanLogFileRow->validation();
        $this->assertSame(false, $humanLogFileRow->isValid());
    }

    public function testShouldFailToCountableRowsHaveAtLeastOneRow()
    {
        $humanLogFile = new HumanLogFileEntity(new DomainEventBus());
        $humanLogFile->create();
        $this->assertSame(0, count($humanLogFile->getRows()));
    }

    /**
     * @testWith ["  1:08 Kill: 3 2 6: Isgalamido killed Mocinha by"]
     *           ["  1:08 Kill:  Isgalamido killed Mo"]
     *           [""]
     */
    public function testShouldFailToRowMatcherIfEntryPatternIsInvalid(string $invalidPatterRow)
    {
        $humanRowMapper = new HumanRowMapper();
        $result = $humanRowMapper->map($invalidPatterRow);
        $this->assertCount(0, $result);
    }

    public function testShouldBeAbleToCreateHumanLogFileSuccessfully()
    {

        $humanLogFile = new HumanLogFileEntity(new DomainEventBus());
        $humanRowMapper = new HumanRowMapper();
        $createHumanFileUseCase = new CreateHumanLogFileUseCase($humanLogFile, $humanRowMapper);
        $createHumanFileUseCase->execute(new CreateHumanLogFileInput($this->getContent(), ['size']));
        $this->assertTrue($humanLogFile->isValid());
    }

    public function getContent(): Generator
    {
        $file = new \SplFileObject(storage_path('app/public/qgames.log'));

        while (!$file->eof()) {
            yield $file->fgets();
        }
    }
}
