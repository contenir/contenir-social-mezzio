<?php

declare(strict_types=1);

namespace Contenir\Social\Mezzio\Tests\Unit\Factory;

use Contenir\Social\Mezzio\Factory\SiteProfilesFactory;
use Contenir\Social\Mezzio\Tests\TestAsset\ArrayContainer;
use Contenir\Social\Mezzio\Tests\TestAsset\Settings;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
final class SiteProfilesFactoryTest extends TestCase
{
    #[Test]
    public function readsTheProfilesFromTheSitesSettings(): void
    {
        $profiles = (new SiteProfilesFactory())(new ArrayContainer(['config' => Settings::withLocal()]));

        static::assertSame(
            [
                'https://www.facebook.com/housesmagazine',
                'https://www.instagram.com/housesmagazine/',
                'https://x.com/HousesLocal',
            ],
            $profiles->sameAs(),
        );
    }

    #[Test]
    public function writesWithTheApplicationsDefaultOptions(): void
    {
        $profiles = (new SiteProfilesFactory())(new ArrayContainer([
            'config' => [...Settings::global(), 'contenir_social' => ['options' => ['block' => 'footer']]],
        ]));

        static::assertStringStartsWith('<ul class="footer footer--profiles">', $profiles->render());
    }
}
