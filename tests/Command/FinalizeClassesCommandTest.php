<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Tests\Command;

use Entropy\Console\Enum\ExitCode;
use Rector\SwissKnife\Command\FinalizeClassesCommand;
use Rector\SwissKnife\Tests\AbstractTestCase;

final class FinalizeClassesCommandTest extends AbstractTestCase
{
    private FinalizeClassesCommand $finalizeClassesCommand;

    private string $originalWorkingDirectory;

    private string $projectDirectory;

    private string $entityFilePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->finalizeClassesCommand = $this->make(FinalizeClassesCommand::class);

        $this->originalWorkingDirectory = (string) getcwd();
        $this->projectDirectory = sys_get_temp_dir() . '/swiss-knife-finalize-' . uniqid();
        $this->entityFilePath = $this->projectDirectory . '/src/Entity/User.php';

        mkdir($this->projectDirectory . '/src/Entity', 0777, true);
        copy(__DIR__ . '/Fixture/FinalizeClasses/User.php.inc', $this->entityFilePath);

        chdir($this->projectDirectory);
    }

    protected function tearDown(): void
    {
        chdir($this->originalWorkingDirectory);

        unlink($this->entityFilePath);
        rmdir(dirname($this->entityFilePath));
        rmdir($this->projectDirectory . '/src');
        @unlink($this->projectDirectory . '/composer.json');
        rmdir($this->projectDirectory);

        parent::tearDown();
    }

    public function testDoctrineEntityIsNotFinalizedWithoutFlag(): void
    {
        $this->copyComposerJsonFixture('supported-composer.json');

        $this->assertSame(ExitCode::SUCCESS, $this->runCommand(false));
        $this->assertStringNotContainsString('final class', (string) file_get_contents($this->entityFilePath));
    }

    /**
     * @dataProvider provideFlagData
     */
    public function testFlagRequiresPhp84AndDoctrineOrm34(?string $composerJsonFixture, int $expectedExitCode): void
    {
        if ($composerJsonFixture !== null) {
            $this->copyComposerJsonFixture($composerJsonFixture);
        }

        $this->assertSame($expectedExitCode, $this->runCommand(true));
        $isFinalized = str_contains((string) file_get_contents($this->entityFilePath), 'final class User');
        $this->assertSame($expectedExitCode === ExitCode::SUCCESS, $isFinalized);
    }

    /**
     * @return iterable<array{null|string, int}>
     */
    public static function provideFlagData(): iterable
    {
        yield ['supported-composer.json', ExitCode::SUCCESS];
        yield ['php-too-low-composer.json', ExitCode::ERROR];
        yield ['doctrine-dev-too-low-composer.json', ExitCode::ERROR];
        yield [null, ExitCode::ERROR];
    }

    private function copyComposerJsonFixture(string $fixtureName): void
    {
        copy(__DIR__ . '/Fixture/FinalizeClasses/' . $fixtureName, $this->projectDirectory . '/composer.json');
    }

    private function runCommand(bool $finalizeDoctrineEntities): int
    {
        ob_start();
        try {
            return $this->finalizeClassesCommand->run(
                paths: [$this->projectDirectory . '/src'],
                noProgress: true,
                finalizeDoctrineEntities: $finalizeDoctrineEntities,
            );
        } finally {
            ob_end_clean();
        }
    }
}
