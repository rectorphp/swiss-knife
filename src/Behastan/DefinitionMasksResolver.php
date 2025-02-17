<?php

declare (strict_types=1);
namespace Rector\SwissKnife\Behastan;

use SwissKnife202502\PhpParser\Comment\Doc;
use SwissKnife202502\PhpParser\Node\Name;
use SwissKnife202502\PhpParser\Node\Scalar\String_;
use SwissKnife202502\PhpParser\Node\Stmt;
use SwissKnife202502\PhpParser\Node\Stmt\Class_;
use SwissKnife202502\PhpParser\NodeFinder;
use SwissKnife202502\PhpParser\NodeTraverser;
use SwissKnife202502\PhpParser\NodeVisitor\NameResolver;
use SwissKnife202502\PhpParser\ParserFactory;
use Rector\SwissKnife\Behastan\ValueObject\ClassMethodContextDefinition;
use Rector\SwissKnife\Behastan\ValueObject\Mask\ExactMask;
use Rector\SwissKnife\Behastan\ValueObject\Mask\NamedMask;
use Rector\SwissKnife\Behastan\ValueObject\Mask\RegexMask;
use Rector\SwissKnife\Behastan\ValueObject\Mask\SkippedMask;
use Rector\SwissKnife\Behastan\ValueObject\MaskCollection;
use SplFileInfo;
final class DefinitionMasksResolver
{
    /**
     * @var string
     */
    private const INSTRUCTION_DOCBLOCK_REGEX = '#\\@(Given|Then|When)\\s+(?<instruction>.*?)\\n#m';
    /**
     * @var string[]
     */
    private const ATTRIBUTE_NAMES = ['SwissKnife202502\\Behat\\Step\\Then', 'SwissKnife202502\\Behat\\Step\\Given', 'SwissKnife202502\\Behat\\Step\\And'];
    /**
     * @param SplFileInfo[] $contextFiles
     */
    public function resolve(array $contextFiles) : MaskCollection
    {
        $masks = [];
        $classMethodContextDefinitions = $this->resolveMasksFromFiles($contextFiles);
        foreach ($classMethodContextDefinitions as $classMethodContextDefinition) {
            $rawMask = $classMethodContextDefinition->getMask();
            // @todo edge case - handle next
            if (\strpos($rawMask, ' [:') !== \false) {
                $masks[] = new SkippedMask($rawMask, $classMethodContextDefinition->getFilePath(), $classMethodContextDefinition->getClass(), $classMethodContextDefinition->getMethodName());
                continue;
            }
            // regex pattern, handled else-where
            if (\strncmp($rawMask, '/', \strlen('/')) === 0) {
                $masks[] = new RegexMask($rawMask, $classMethodContextDefinition->getFilePath(), $classMethodContextDefinition->getClass(), $classMethodContextDefinition->getMethodName());
                continue;
            }
            // handled in mask one
            \preg_match('#(\\:[\\W\\w]+)#', $rawMask, $match);
            if ($match !== []) {
                //  if (str_contains($rawMask, ':')) {
                $masks[] = new NamedMask($rawMask, $classMethodContextDefinition->getFilePath(), $classMethodContextDefinition->getClass(), $classMethodContextDefinition->getMethodName());
                continue;
            }
            $masks[] = new ExactMask($rawMask, $classMethodContextDefinition->getFilePath(), $classMethodContextDefinition->getClass(), $classMethodContextDefinition->getMethodName());
        }
        return new MaskCollection($masks);
    }
    /**
     * @param SplFileInfo[] $fileInfos
     *
     * @return ClassMethodContextDefinition[]
     */
    private function resolveMasksFromFiles(array $fileInfos) : array
    {
        $classMethodContextDefinitions = [];
        $parserFactory = new ParserFactory();
        $nodeFinder = new NodeFinder();
        $phpParser = $parserFactory->createForHostVersion();
        $nodeTraverser = new NodeTraverser();
        $nodeTraverser->addVisitor(new NameResolver());
        foreach ($fileInfos as $fileInfo) {
            /** @var string $fileContents */
            $fileContents = \file_get_contents($fileInfo->getRealPath());
            /** @var Stmt[] $stmts */
            $stmts = $phpParser->parse($fileContents);
            $nodeTraverser->traverse($stmts);
            // 1. get class name
            $class = $nodeFinder->findFirstInstanceOf($stmts, Class_::class);
            if (!$class instanceof Class_) {
                continue;
            }
            if ($class->isAnonymous()) {
                continue;
            }
            if (!$class->namespacedName instanceof Name) {
                continue;
            }
            $className = $class->namespacedName->toString();
            foreach ($class->getMethods() as $classMethod) {
                $methodName = $classMethod->name->toString();
                // 1. collect from docblock
                if ($classMethod->getDocComment() instanceof Doc) {
                    \preg_match_all(self::INSTRUCTION_DOCBLOCK_REGEX, $classMethod->getDocComment()->getText(), $match);
                    foreach ($match['instruction'] as $instruction) {
                        $mask = $this->clearMask($instruction);
                        $classMethodContextDefinitions[] = new ClassMethodContextDefinition($fileInfo->getRealPath(), $className, $methodName, $mask);
                    }
                }
                // 2. collect from attributes
                foreach ($classMethod->attrGroups as $attrGroup) {
                    foreach ($attrGroup->attrs as $attr) {
                        $attributeName = $attr->name->toString();
                        if (!\in_array($attributeName, self::ATTRIBUTE_NAMES)) {
                            continue;
                        }
                        $firstArgValue = $attr->args[0]->value;
                        if (!$firstArgValue instanceof String_) {
                            continue;
                        }
                        $classMethodContextDefinitions[] = new ClassMethodContextDefinition($fileInfo->getRealPath(), $className, $methodName, $firstArgValue->value);
                    }
                }
            }
        }
        return $classMethodContextDefinitions;
    }
    private function clearMask(string $mask) : string
    {
        $mask = \trim($mask);
        // clear extra quote escaping that would cause miss-match with feature masks
        $mask = \str_replace('\\\'', "'", $mask);
        return \str_replace('\\/', '/', $mask);
    }
}
