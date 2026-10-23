<?php

declare(strict_types=1);

namespace Contenir\Social\Mezzio\Tests\Unit\View\Helper;

use Contenir\Social\Html\Options;
use Contenir\Social\Mezzio\SiteProfiles;
use Contenir\Social\Mezzio\View\Helper\SocialProfiles;
use Contenir\Social\Network;
use Contenir\Social\Profiles;
use Contenir\Social\ProfileUrl;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
final class SocialProfilesTest extends TestCase
{
    private static function service(): SiteProfiles
    {
        return new SiteProfiles(
            new Profiles([ProfileUrl::parse(Network::Instagram, '@houses')]),
            new Options(
                block: 'footer',
            ),
        );
    }

    #[Test]
    public function givesTheServicesAddresses(): void
    {
        $helper = new SocialProfiles(self::service());

        static::assertSame(
            [['https://www.instagram.com/houses/'], ['instagram' => 'https://www.instagram.com/houses/']],
            [$helper->sameAs(), $helper->urls()],
        );
    }

    #[Test]
    public function givesTheServicesProfiles(): void
    {
        $service = self::service();

        static::assertSame($service->profiles(), (new SocialProfiles($service))->profiles());
    }

    #[Test]
    public function writesWhatTheServiceWrites(): void
    {
        static::assertSame(
            self::service()->render(['labels' => true]),
            (new SocialProfiles(self::service()))(['labels' => true]),
        );
    }
}
