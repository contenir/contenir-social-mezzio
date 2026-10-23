<?php

declare(strict_types=1);

namespace Contenir\Social\Mezzio\Tests\Unit\View\Helper;

use Contenir\Social\Mezzio\SiteShare;
use Contenir\Social\Mezzio\View\Helper\Share;
use Contenir\Social\Script\ShareScript;
use Contenir\Social\Share\Page;
use Contenir\Social\Share\ShareConfig;
use Contenir\Social\Share\ShareTarget;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
final class ShareTest extends TestCase
{
    private static function page(): Page
    {
        return new Page('https://www.example.com/winners', 'Winners');
    }

    private static function service(): SiteShare
    {
        return new SiteShare(new ShareConfig([ShareTarget::X, ShareTarget::CopyLink]));
    }

    #[Test]
    public function givesTheServicesLinks(): void
    {
        static::assertEquals(self::service()->links(self::page()), (new Share(self::service()))->links(self::page()));
    }

    #[Test]
    public function writesTheScriptWithTheNonceItIsGiven(): void
    {
        static::assertSame(ShareScript::tag('n0nce'), (new Share(self::service()))->script('n0nce'));
    }

    #[Test]
    public function writesWhatTheServiceWrites(): void
    {
        static::assertSame(
            self::service()->render(self::page(), ['nonce' => 'n0nce']),
            (new Share(self::service()))(self::page(), ['nonce' => 'n0nce']),
        );
    }
}
