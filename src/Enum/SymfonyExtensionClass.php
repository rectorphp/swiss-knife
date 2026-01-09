<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Enum;

final class SymfonyExtensionClass
{
    public const string SENTRY = 'Sentry\SentryBundle\DependencyInjection\SentryExtension';

    public const string DOCTRINE_MIGRATIONS = 'Doctrine\Bundle\MigrationsBundle\DependencyInjection\DoctrineMigrationsExtension';

    public const string FRAMEWORK = 'Symfony\Bundle\FrameworkBundle\DependencyInjection\FrameworkExtension';

    public const string MONOLOG = 'Symfony\Bundle\MonologBundle\DependencyInjection\MonologExtension';

    public const string SECURITY = 'Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension';

    public const string TWIG = 'Symfony\Bundle\TwigBundle\DependencyInjection\TwigExtension';

    public const string DOCTRINE = 'Doctrine\Bundle\DoctrineBundle\DependencyInjection\DoctrineExtension';

    public const string WEBPROFILER = 'Symfony\Bundle\WebProfilerBundle\DependencyInjection\WebProfilerExtension';
}
