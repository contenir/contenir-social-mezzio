<?php

declare(strict_types=1);

namespace Contenir\Social\Mezzio\Tests\Integration;

use Contenir\Social\Mezzio\SiteProfiles;
use Contenir\Social\Mezzio\SiteShare;
use Contenir\Social\Mezzio\Tests\TestAsset\PageMetadata;
use Contenir\Social\Mezzio\Tests\TestAsset\Settings;
use Contenir\Social\Mezzio\Tests\Trait\RendersViewsTrait;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * The framework-free services from a laminas-servicemanager container built
 * from the package's ConfigProvider, as a site's handlers and other template
 * engines use them.
 */
#[Group('integration')]
final class ContainerTest extends TestCase
{
    use RendersViewsTrait;

    #[Test]
    public function givesOneShareServiceForEveryPage(): void
    {
        $this->setUpViews(Settings::global());
        $share = $this->services->get(SiteShare::class);

        static::assertSame(
            [
                'https://x.com/intent/tweet?text=One&url=https%3A%2F%2Fwww.example.com%2Fone',
                'https://x.com/intent/tweet?text=Two&url=https%3A%2F%2Fwww.example.com%2Ftwo',
                true,
            ],
            [
                $share->links(new PageMetadata('https://www.example.com/one', 'One'))[0]->href,
                $share->links(['url' => '/two', 'title' => 'Two'])[0]->href,
                $share === $this->services->get(SiteShare::class),
            ],
        );
    }

    #[Test]
    public function makesAddressesFromTheRootAbsoluteOnTheLocalSiteAddress(): void
    {
        $this->setUpViews(Settings::withLocal());

        static::assertSame(
            'http://houses.localhost:8080/winners',
            $this->services->get(SiteShare::class)->links(['url' => '/winners'])[1]->url,
        );
    }

    #[Test]
    public function readsTheProfilesFromTheSitesSettings(): void
    {
        $this->setUpViews(Settings::global());

        static::assertSame(
            [
                'facebook'  => 'https://www.facebook.com/housesmagazine',
                'instagram' => 'https://www.instagram.com/housesmagazine/',
                'x'         => 'https://x.com/HousesMag',
            ],
            $this->services->get(SiteProfiles::class)->urls(),
        );
    }
}
