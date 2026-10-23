<?php

declare(strict_types=1);

namespace Contenir\Social\Mezzio\Tests\Unit;

use Contenir\Social\Mezzio\ConfigProvider;
use Contenir\Social\Mezzio\Factory\ShareHelperFactory;
use Contenir\Social\Mezzio\Factory\SiteProfilesFactory;
use Contenir\Social\Mezzio\Factory\SiteShareFactory;
use Contenir\Social\Mezzio\Factory\SocialProfilesHelperFactory;
use Contenir\Social\Mezzio\SiteProfiles;
use Contenir\Social\Mezzio\SiteShare;
use Contenir\Social\Mezzio\View\Helper\Share;
use Contenir\Social\Mezzio\View\Helper\SocialProfiles;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
final class ConfigProviderTest extends TestCase
{
    #[Test]
    public function givesEachPartOnItsOwnForAnApplicationThatMergesThem(): void
    {
        $provider = new ConfigProvider();

        static::assertSame(
            ['dependencies' => $provider->getDependencies(), 'view_helpers' => $provider->getViewHelpers()],
            $provider(),
        );
    }

    #[Test]
    public function registersBothHelpersUnderTheirNames(): void
    {
        static::assertSame(
            [
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
            ],
            (new ConfigProvider())()['view_helpers'],
        );
    }

    #[Test]
    public function registersBothServices(): void
    {
        static::assertSame(
            [
                'factories' => [
                    SiteProfiles::class => SiteProfilesFactory::class,
                    SiteShare::class    => SiteShareFactory::class,
                ],
            ],
            (new ConfigProvider())()['dependencies'],
        );
    }
}
