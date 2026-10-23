<?php

declare(strict_types=1);

namespace Contenir\Social\Mezzio\Factory;

use Contenir\Social\Mezzio\SiteProfiles;
use Contenir\Social\Mezzio\View\Helper\SocialProfiles;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

/**
 * Builds the socialProfiles() view helper around the application's
 * SiteProfiles service.
 *
 * @api
 */
final class SocialProfilesHelperFactory
{
    /**
     * @throws ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container): SocialProfiles
    {
        return new SocialProfiles($container->get(SiteProfiles::class));
    }
}
