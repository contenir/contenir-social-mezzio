<?php

declare(strict_types=1);

namespace Contenir\Social\Mezzio\Tests\Unit\Factory;

use Contenir\Social\Mezzio\Factory\ShareHelperFactory;
use Contenir\Social\Mezzio\Factory\SocialProfilesHelperFactory;
use Contenir\Social\Mezzio\SiteProfiles;
use Contenir\Social\Mezzio\SiteShare;
use Contenir\Social\Mezzio\Tests\TestAsset\ArrayContainer;
use Contenir\Social\Network;
use Contenir\Social\Profiles;
use Contenir\Social\ProfileUrl;
use Contenir\Social\Share\Page;
use Contenir\Social\Share\ShareConfig;
use Contenir\Social\Share\ShareTarget;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Building the view helpers around the application's services, as
 * laminas-view's plugin manager does with its parent container.
 */
#[Group('unit')]
final class HelperFactoriesTest extends TestCase
{
    #[Test]
    public function buildsShareAroundTheApplicationsService(): void
    {
        $share  = new SiteShare(new ShareConfig([ShareTarget::CopyLink]));
        $helper = (new ShareHelperFactory())(new ArrayContainer([SiteShare::class => $share]));
        $page   = new Page('https://www.example.com/winners');

        static::assertSame($share->render($page, ['nonce' => 'n']), $helper($page, ['nonce' => 'n']));
    }

    #[Test]
    public function buildsSocialProfilesAroundTheApplicationsService(): void
    {
        $profiles = new Profiles([ProfileUrl::parse(Network::Instagram, 'houses')]);
        $helper   = (new SocialProfilesHelperFactory())(new ArrayContainer([
            SiteProfiles::class => new SiteProfiles($profiles),
        ]));

        static::assertSame($profiles, $helper->profiles());
    }
}
