<?php

declare(strict_types=1);

namespace Contenir\Social\Mezzio\Tests\Unit;

use Contenir\Social\Html\Options;
use Contenir\Social\Html\ShareButtons;
use Contenir\Social\Mezzio\SiteShare;
use Contenir\Social\Mezzio\Tests\TestAsset\PageMetadata;
use Contenir\Social\Script\ShareScript;
use Contenir\Social\Share\Page;
use Contenir\Social\Share\ShareConfig;
use Contenir\Social\Share\ShareLinks;
use Contenir\Social\Share\ShareTarget;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
final class SiteShareTest extends TestCase
{
    private const string URL = 'https://www.example.com/winners';

    /**
     * @param list<ShareTarget> $targets
     */
    private static function buttons(array $targets, Options $options, ?Page $page = null): string
    {
        $links = (new ShareLinks(new ShareConfig($targets)))->for($page ?? new Page(self::URL, 'Winners'));

        return ShareButtons::render($links, $options);
    }

    private static function page(): PageMetadata
    {
        return new PageMetadata(self::URL, 'Winners');
    }

    #[Test]
    public function givesThePagesLinksForMarkupOfASitesOwn(): void
    {
        $share = new SiteShare(new ShareConfig([ShareTarget::LinkedIn]), baseUrl: 'https://www.example.com');

        static::assertSame(
            'https://www.linkedin.com/sharing/share-offsite/?url=https%3A%2F%2Fwww.example.com%2F1',
            $share->links(['url' => '/1'])[0]->href,
        );
    }

    #[Test]
    public function givesTheSitesShareSettings(): void
    {
        $config = new ShareConfig([ShareTarget::X]);

        static::assertSame($config, (new SiteShare($config))->config());
    }

    #[Test]
    public function leavesTheScriptOutWhenAsked(): void
    {
        $share = new SiteShare(new ShareConfig([ShareTarget::CopyLink]));

        static::assertSame(
            self::buttons([ShareTarget::CopyLink], new Options()),
            $share->render(self::page(), ['script' => false]),
        );
    }

    #[Test]
    public function sharesThePageItIsGiven(): void
    {
        $share = new SiteShare(new ShareConfig([ShareTarget::X], labels: false));
        $page  = new Page('https://www.example.com/entry/1', 'Entry 1');

        static::assertSame(self::buttons([ShareTarget::X], new Options(labels: false), $page), $share->render($page));
    }

    #[Test]
    public function takesOptionsAsTheyAre(): void
    {
        $share   = new SiteShare(new ShareConfig([ShareTarget::CopyLink]));
        $options = new Options(block: 'post');

        static::assertSame(
            self::buttons([ShareTarget::CopyLink], $options) . ShareScript::tag(),
            $share->render(self::page(), $options),
        );
    }

    #[Test]
    public function usesTheApplicationsDefaultsOverTheSitesLabels(): void
    {
        $options = new Options(
            icons: false,
            labels: false,
            newTab: false,
            block: 'post',
            class: 'post__share',
        );
        $share = new SiteShare(new ShareConfig([ShareTarget::X], labels: true), $options);

        static::assertSame(self::buttons([ShareTarget::X], $options), $share->render(self::page()));
    }

    #[Test]
    public function writesNothingWithoutButtons(): void
    {
        static::assertSame('', (new SiteShare(new ShareConfig([])))->render(self::page()));
    }

    #[Test]
    public function writesTheButtonsWithoutAScriptWhenNoneIsNeeded(): void
    {
        $share = new SiteShare(new ShareConfig([ShareTarget::X], labels: false));

        static::assertSame(self::buttons([ShareTarget::X], new Options(labels: false)), $share->render(self::page()));
    }

    #[Test]
    public function writesTheScriptAfterTheButtonsEveryTimeTheyNeedIt(): void
    {
        $share = new SiteShare(new ShareConfig([ShareTarget::X, ShareTarget::CopyLink]));
        $html  = self::buttons([ShareTarget::X, ShareTarget::CopyLink], new Options(labels: true))
        . ShareScript::tag('n0nce');

        static::assertSame(
            [$html, $html],
            [$share->render(self::page(), ['nonce' => 'n0nce']), $share->render(self::page(), ['nonce' => 'n0nce'])],
        );
    }

    #[Test]
    public function writesTheScriptOnItsOwnWithTheNonceItIsGiven(): void
    {
        static::assertSame(ShareScript::tag('a'), (new SiteShare(new ShareConfig()))->script('a'));
    }

    #[Test]
    public function writesTheScriptWithoutANonceWhenTheNonceIsNotText(): void
    {
        $share = new SiteShare(new ShareConfig([ShareTarget::Share]));

        static::assertStringEndsWith('</ul>' . ShareScript::tag(), $share->render(self::page(), ['nonce' => 12]));
    }
}
