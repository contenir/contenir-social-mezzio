<?php

declare(strict_types=1);

namespace Contenir\Social\Mezzio\Factory;

use Contenir\Social\Mezzio\SiteShare;
use Contenir\Social\Mezzio\View\Helper\Share;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

/**
 * Builds the share() view helper around the application's SiteShare
 * service.
 *
 * @api
 */
final class ShareHelperFactory
{
    /**
     * @throws ContainerExceptionInterface
     */
    public function __invoke(ContainerInterface $container): Share
    {
        return new Share($container->get(SiteShare::class));
    }
}
