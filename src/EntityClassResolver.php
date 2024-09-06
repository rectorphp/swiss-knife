<?php

declare(strict_types=1);

namespace Rector\SwissKnife;

use Nette\Utils\Strings;
use PhpParser\NodeTraverser;
use Rector\SwissKnife\Finder\PhpFilesFinder;
use Rector\SwissKnife\Finder\YamlFilesFinder;
use Rector\SwissKnife\PhpParser\CachedPhpParser;
use Rector\SwissKnife\PhpParser\NodeTraverserFactory;
use Rector\SwissKnife\PhpParser\NodeVisitor\EntityClassNameCollectingNodeVisitor;
use Symfony\Component\Finder\SplFileInfo;
use Webmozart\Assert\Assert;

/**
 * @see \Rector\SwissKnife\Tests\EntityClassResolver\EntityClassResolverTest
 */
final readonly class EntityClassResolver
{
    /**
     * @var string
     * @see https://regex101.com/r/YFbH1x/1
     */
    private const YAML_ENTITY_CLASS_NAME_REGEX = '#^(?<class_name>[\w+\\\\]+)\:\n#m';

    public function __construct(
        private CachedPhpParser $cachedPhpParser
    ) {
    }

    /**
     * @param string[] $paths
     * @return string[]
     */
    public function resolve(array $paths, ?callable $progressClosure = null): array
    {
        Assert::allString($paths);
        Assert::allFileExists($paths);

        // 1. resolve from yaml annotations
        $yamlEntityClassNames = $this->resolveYamlEntityClassNames($paths);

        // 2. resolve from direct class names with namespace parts, doctrine annotation or docblock
        $phpFileInfos = PhpFilesFinder::find($paths);
        $entityClassNameCollectingNodeVisitor = new EntityClassNameCollectingNodeVisitor();

        $nodeTraverser = NodeTraverserFactory::create($entityClassNameCollectingNodeVisitor);
        $this->traverseFileInfos($phpFileInfos, $nodeTraverser, $progressClosure);

        $markedEntityClassNames = $entityClassNameCollectingNodeVisitor->getEntityClassNames();

        $entityClassNames = array_merge($yamlEntityClassNames, $markedEntityClassNames);
        sort($entityClassNames);

        return array_unique($entityClassNames);
    }

    /**
     * @param SplFileInfo[] $phpFileInfos
     */
    private function traverseFileInfos(
        array $phpFileInfos,
        NodeTraverser $nodeTraverser,
        ?callable $progressClosure = null
    ): void {
        foreach ($phpFileInfos as $phpFileInfo) {
            $stmts = $this->cachedPhpParser->parseFile($phpFileInfo->getRealPath());

            $nodeTraverser->traverse($stmts);

            if (is_callable($progressClosure)) {
                $progressClosure();
            }
        }
    }

    /**
     * @param string[] $paths
     * @return string[]
     */
    private function resolveYamlEntityClassNames(array $paths): array
    {
        $yamlFileInfos = YamlFilesFinder::find($paths);

        $yamlEntityClassNames = [];

        /** @var SplFileInfo $yamlFileInfo */
        foreach ($yamlFileInfos as $yamlFileInfo) {
            $matches = Strings::matchAll($yamlFileInfo->getContents(), self::YAML_ENTITY_CLASS_NAME_REGEX);

            foreach ($matches as $match) {
                $yamlEntityClassNames[] = $match['class_name'];
            }
        }

        return $yamlEntityClassNames;
    }
}
