<?php

declare(strict_types=1);

namespace Contenir\Social\Mezzio\Tests\TestAsset;

/**
 * A Mezzio site's config as the CMS leaves it: settings.global.php merged at
 * the top level, and settings.local.php under "settings".
 */
final class Settings
{
    /**
     * Profiles as the core Social Media Links plugin left them, and share
     * buttons chosen in the add-on, as settings.global.php holds them.
     *
     * @return array<string, mixed>
     */
    public static function global(): array
    {
        return [
            'site'       => ['title' => 'Houses', 'url' => 'https://www.example.com'],
            'social'     => [
                'social_facebook'  => 'https://www.facebook.com/housesmagazine',
                'social_twitter'   => '@HousesMag',
                'social_instagram' => 'housesmagazine',
                'social_linkedin'  => null,
            ],
            'extensions' => [
                'enabled' => ['social'],
                'social'  => [
                    'share_networks' => [
                        ['fields' => ['network' => 'x']],
                        ['fields' => ['network' => 'copy']],
                    ],
                    'share_labels'   => '0',
                    'share_utm'      => '0',
                ],
            ],
        ];
    }

    /**
     * The global settings with a local override under "settings", as the
     * CMS writes settings.local.php: another address for the site, and an
     * X account changed on this server.
     *
     * @return array<string, mixed>
     */
    public static function withLocal(): array
    {
        return [
            ...self::global(),
            'settings' => [
                'site'   => ['url' => 'http://houses.localhost:8080'],
                'social' => ['social_twitter' => 'HousesLocal'],
            ],
        ];
    }
}
