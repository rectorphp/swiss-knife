<?php

declare (strict_types=1);
namespace EasyCI20220607\Symplify\EasyCI\Console\Output;

use EasyCI20220607\Symfony\Component\Console\Command\Command;
use EasyCI20220607\Symfony\Component\Console\Style\SymfonyStyle;
use EasyCI20220607\Symplify\EasyCI\Contract\ValueObject\FileErrorInterface;
final class FileErrorsReporter
{
    /**
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    public function __construct(SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
    }
    /**
     * @param FileErrorInterface[] $fileErrors
     */
    public function report(array $fileErrors) : int
    {
        if ($fileErrors === []) {
            $this->symfonyStyle->success('No errors found');
            return Command::SUCCESS;
        }
        foreach ($fileErrors as $fileError) {
            $this->symfonyStyle->writeln($fileError->getRelativeFilePath());
            $this->symfonyStyle->writeln(' * ' . $fileError->getErrorMessage());
            $this->symfonyStyle->newLine();
        }
        $errorMassage = \sprintf('%d errors found', \count($fileErrors));
        $this->symfonyStyle->error($errorMassage);
        return Command::FAILURE;
    }
}
