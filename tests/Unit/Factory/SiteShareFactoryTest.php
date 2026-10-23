<?php

declare(strict_types=1);

namespace Contenir\Social\Mezzio\Tests\Unit\Factory;

use Contenir\Social\Mezzio\Factory\SiteShareFactory;
use Contenir\Social\Mezzio\Tests\TestAsset\ArrayContainer;
use Contenir\Social\Mezzio\Tests\TestAsset\Settings;
use Contenir\Social\Share\ShareTarget;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
final class SiteShareFactoryTest extends TestCase
{
    #[Test]
    public function makesAddressesFromTheRootAbsoluteOnTheSitesAddress(): void
    {
        $share = (new SiteShareFactory())(new ArrayContainer(['config' => Settings::global()]));

        static::assertSame('https://www.example.com/winners', $share->links(['url' => '/winners'])[0]->url);
    }

    #[Test]
    public function readsTheButtonsFromTheSitesSettings(): void
    {
        $share = (new SiteShareFactory())(new ArrayContainer(['config' => Settings::global()]));

        static::assertSame(
            [[ShareTarget::X, ShareTarget::CopyLink], false],
            [$share->config()->targets, $share->config()->labels],
        );
    }

    #[Test]
    public function writesWithTheApplicationsDefaultOptions(): void
    {
        $share = (new SiteShareFactory())(new ArrayContainer([
            'config' => [...Settings::global(), 'contenir_social' => ['options' => ['block' => 'post']]],
        ]));

        static::assertStringStartsWith(
            '<ul class="post post--share">',
            $share->render(['url' => 'https://www.example.com/winners']),
        );
    }
}
