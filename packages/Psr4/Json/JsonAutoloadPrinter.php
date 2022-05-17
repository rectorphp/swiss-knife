<?php

declare (strict_types=1);
namespace Symplify\EasyCI\Psr4\Json;

use EasyCI20220517\Nette\Utils\Json;
use EasyCI20220517\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection;
use Symplify\EasyCI\Psr4\FileSystem\Psr4PathNormalizer;
use Symplify\EasyCI\Psr4\ValueObject\Psr4NamespaceToPaths;
final class JsonAutoloadPrinter
{
    /**
     * @var \Symplify\EasyCI\Psr4\FileSystem\Psr4PathNormalizer
     */
    private $psr4PathNormalizer;
    public function __construct(\Symplify\EasyCI\Psr4\FileSystem\Psr4PathNormalizer $psr4PathNormalizer)
    {
        $this->psr4PathNormalizer = $psr4PathNormalizer;
    }
    /**
     * @param Psr4NamespaceToPaths[] $psr4NamespaceToPaths
     */
    public function createJsonAutoloadContent(array $psr4NamespaceToPaths) : string
    {
        $normalizedJsonArray = $this->psr4PathNormalizer->normalizePsr4NamespaceToPathsToJsonsArray($psr4NamespaceToPaths);
        $composerJson = [\EasyCI20220517\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::AUTOLOAD => ['psr-4' => $normalizedJsonArray]];
        return \EasyCI20220517\Nette\Utils\Json::encode($composerJson, \EasyCI20220517\Nette\Utils\Json::PRETTY);
    }
}
