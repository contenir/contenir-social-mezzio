<?php

declare(strict_types=1);

namespace Contenir\Social\Mezzio\Factory;

use Contenir\Social\Mezzio\SiteConfig;
use Contenir\Social\Mezzio\SiteShare;
use Contenir\Social\Share\ShareConfig;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

/**
 * Builds SiteShare from the site's share settings, as the CMS writes them,
 * the site's address (settings site.url) and the default options in
 * config['contenir_social']['options'].
 *
 * @api
 */
final class SiteShareFactory
{
    /**
     * @throws ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container): SiteShare
    {
        $config = SiteConfig::from($container);

        return new SiteShare(ShareConfig::fromSettings($config->settings()), $config->options(), $config->baseUrl());
    }
}
