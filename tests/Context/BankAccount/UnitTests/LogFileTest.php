<?php

namespace Tests\Context\LogHandler\UnitTests;

use DG\BypassFinals;
use Domains\Context\LogHandler\Application\UseCases\LogFile\SelectLogFileException;
use Domains\Context\LogHandler\Application\UseCases\LogFile\SelectLogFileInput;
use Domains\Context\LogHandler\Application\UseCases\LogFile\SelectLogFileUseCase;
use Domains\Context\LogHandler\Domain\Model\LogFile\LogFileEntity;
use Domains\Context\LogHandler\Domain\Model\LogFile\LogFileMetadata;
use Domains\CrossCutting\Domain\Application\Event\Bus\DomainEventBus;
use Tests\TestCase;

class LogFileTest extends TestCase
{

    public static function setUpBeforeClass(): void
    {
        BypassFinals::enable();
    }

    public function testShouldFailToFileMetadataValuesAreMissing()
    {
        $file = new \SplFileObject(storage_path('app/public/qgames.log'));
        $logFile = new LogFileEntity(new DomainEventBus());
        $logFile->extractOf($file, new LogFileMetadata(0, 'log'));
        $this->assertFalse($logFile->isValid());
    }

    public function testShouldFailToLogFileForInvalidName()
    {
        $this->expectException(SelectLogFileException::class);

        $logFile = new LogFileEntity(new DomainEventBus());
        $selectFileUseCase = new SelectLogFileUseCase($logFile);
        $selectFileUseCase->execute(new SelectLogFileInput('invalid_filename.log'));
    }

    public function testShouldBeAbleToSelectLogFileSuccessfully()
    {

        $logFile = \Mockery::spy(new LogFileEntity(new DomainEventBus()));
        $selectFileUseCase = new SelectLogFileUseCase($logFile);
        $selectFileUseCase->execute(new SelectLogFileInput('qgames.log'));
        $logFile->shouldHaveReceived('extractOf')->once();
        $this->assertTrue($logFile->isValid());
    }
}
