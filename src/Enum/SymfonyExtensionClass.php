<?php

declare(strict_types=1);

namespace Rector\SwissKnife\Enum;

final class SymfonyExtensionClass
{
    /**
     * @var string
     */
    public const SENTRY = 'Sentry\SentryBundle\DependencyInjection\SentryExtension';

    /**
     * @var string
     */
    public const DOCTRINE_MIGRATIONS = 'Doctrine\Bundle\MigrationsBundle\DependencyInjection\DoctrineMigrationsExtension';

    /**
     * @var string
     */
    public const FRAMEWORK = 'Symfony\Bundle\FrameworkBundle\DependencyInjection\FrameworkExtension';

    /**
     * @var string
     */
    public const MONOLOG = 'Symfony\Bundle\MonologBundle\DependencyInjection\MonologExtension';

    /**
     * @var string
     */
    public const SECURITY = 'Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension';

    /**
     * @var string
     */
    public const TWIG = 'Symfony\Bundle\TwigBundle\DependencyInjection\TwigExtension';

    /**
     * @var string
     */
    public const DOCTRINE = 'Doctrine\Bundle\DoctrineBundle\DependencyInjection\DoctrineExtension';

    /**
     * @var string
     */
    public const WEBPROFILER = 'Symfony\Bundle\WebProfilerBundle\DependencyInjection\WebProfilerExtension';
}
