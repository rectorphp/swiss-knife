<?php

declare(strict_types=1);

namespace TomasVotruba\Lemonade\Resolver;

use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Name;
use Webmozart\Assert\Assert;

final class BareRegisteredServicesResolver
{
    /**
     * @param MethodCall[] $bareSetMethodCalls
     * @return array<string, string>
     */
    public function resolveNameToConfigFile(array $bareSetMethodCalls): array
    {
        $servicesToFiles = [];

        foreach ($bareSetMethodCalls as $bareSetMethodCall) {
            $serviceArg = $bareSetMethodCall->getArgs()[0];

            if (! $serviceArg->value instanceof ClassConstFetch) {
                continue;
            }

            $classConstFetch = $serviceArg->value;
            if (! $classConstFetch->class instanceof Name) {
                continue;
            }

            $serviceName = $classConstFetch->class->toString();
            $file = $bareSetMethodCall->getAttribute('file');
            Assert::string($file);

            $servicesToFiles[$serviceName] = $file;
        }

        return $servicesToFiles;
    }
}
