<?php

declare(strict_types=1);

namespace Contenir\Social\Mezzio\Tests\Unit;

use Contenir\Social\Html\Options;
use Contenir\Social\Html\ProfileLinks;
use Contenir\Social\Mezzio\SiteProfiles;
use Contenir\Social\Network;
use Contenir\Social\Profiles;
use Contenir\Social\ProfileUrl;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
final class SiteProfilesTest extends TestCase
{
    private static function profiles(): Profiles
    {
        return new Profiles([
            ProfileUrl::parse(Network::LinkedIn, 'https://www.linkedin.com/company/houses'),
            ProfileUrl::parse(Network::Instagram, '@houses'),
        ]);
    }

    #[Test]
    public function givesEveryAddressByItsNetwork(): void
    {
        static::assertSame(
            [
                'instagram' => 'https://www.instagram.com/houses/',
                'linkedin'  => 'https://www.linkedin.com/company/houses/',
            ],
            (new SiteProfiles(self::profiles()))->urls(),
        );
    }

    #[Test]
    public function givesTheProfilesToTemplatesThatWriteTheirOwn(): void
    {
        $profiles = self::profiles();

        static::assertSame($profiles, (new SiteProfiles($profiles))->profiles());
    }

    #[Test]
    public function listsEveryAddressForSameAs(): void
    {
        static::assertSame(
            ['https://www.instagram.com/houses/', 'https://www.linkedin.com/company/houses/'],
            (new SiteProfiles(self::profiles()))->sameAs(),
        );
    }

    #[Test]
    public function writesInTheSocialBlockWithoutDefaults(): void
    {
        static::assertStringStartsWith(
            '<ul class="social social--profiles">',
            (new SiteProfiles(self::profiles()))->render(),
        );
    }

    #[Test]
    public function writesNothingWithoutProfiles(): void
    {
        static::assertSame([[], [], ''], [
            (new SiteProfiles(new Profiles()))->urls(),
            (new SiteProfiles(new Profiles()))->sameAs(),
            (new SiteProfiles(new Profiles()))->render(),
        ]);
    }

    #[Test]
    public function writesTheProfilesWithTheDefaults(): void
    {
        static::assertSame(
            ProfileLinks::render(self::profiles(), new Options(block: 'footer')),
            (new SiteProfiles(self::profiles(), new Options(block: 'footer')))->render(),
        );
    }

    #[Test]
    public function writesTheProfilesWithTheTemplatesOptions(): void
    {
        static::assertSame(
            ProfileLinks::render(self::profiles(), new Options(
                labels: true,
                block: 'footer',
            )),
            (new SiteProfiles(self::profiles(), new Options(block: 'footer')))->render(['labels' => true]),
        );
    }
}
