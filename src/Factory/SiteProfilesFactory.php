<?php

declare(strict_types=1);

namespace Contenir\Social\Mezzio\Factory;

use Contenir\Social\Mezzio\SiteConfig;
use Contenir\Social\Mezzio\SiteProfiles;
use Contenir\Social\Profiles;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

/**
 * Builds SiteProfiles from the site's settings, as the CMS writes them, and
 * the default options in config['contenir_social']['options'].
 *
 * @api
 */
final class SiteProfilesFactory
{
    /**
     * @throws ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container): SiteProfiles
    {
        $config = SiteConfig::from($container);

        return new SiteProfiles(Profiles::fromSettings($config->settings()), $config->options());
    }
}
