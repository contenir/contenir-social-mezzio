<?php

declare(strict_types=1);

namespace Contenir\Social\Mezzio;

use Contenir\Social\Mezzio\Factory\ShareHelperFactory;
use Contenir\Social\Mezzio\Factory\SiteProfilesFactory;
use Contenir\Social\Mezzio\Factory\SiteShareFactory;
use Contenir\Social\Mezzio\Factory\SocialProfilesHelperFactory;
use Contenir\Social\Mezzio\View\Helper\Share;
use Contenir\Social\Mezzio\View\Helper\SocialProfiles;

/**
 * The services, SiteProfiles and SiteShare, and the laminas-view helpers
 * around them, socialProfiles() and share().
 *
 * @api
 */
final class ConfigProvider
{
    /**
     * @return array{factories: array<class-string, class-string>}
     */
    public function getDependencies(): array
    {
        return [
            'factories' => [
                SiteProfiles::class => SiteProfilesFactory::class,
                SiteShare::class    => SiteShareFactory::class,
            ],
        ];
    }

    /**
     * @return array{aliases: array<string, class-string>, factories: array<class-string, class-string>}
     */
    public function getViewHelpers(): array
    {
        return [
            'aliases'   => [
                'socialProfiles' => SocialProfiles::class,
                'SocialProfiles' => SocialProfiles::class,
                'share'          => Share::class,
                'Share'          => Share::class,
            ],
            'factories' => [
                SocialProfiles::class => SocialProfilesHelperFactory::class,
                Share::class          => ShareHelperFactory::class,
            ],
        ];
    }

    /**
     * @return array{
     *     dependencies: array{factories: array<class-string, class-string>},
     *     view_helpers: array{aliases: array<string, class-string>, factories: array<class-string, class-string>},
     * }
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'view_helpers' => $this->getViewHelpers(),
        ];
    }
}
