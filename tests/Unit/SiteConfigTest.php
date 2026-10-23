<?php

declare(strict_types=1);

namespace Contenir\Social\Mezzio\Tests\Unit;

use Contenir\Social\Mezzio\SiteConfig;
use Contenir\Social\Mezzio\Tests\TestAsset\ArrayContainer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
final class SiteConfigTest extends TestCase
{
    /**
     * @return array<string, array{mixed}>
     */
    public static function siteWithoutAnAddress(): array
    {
        return [
            'no site'             => [['other' => true]],
            'site not an array'   => [['site' => 'Houses']],
            'no address'          => [['site' => ['title' => 'Houses']]],
            'address not text'    => [['site' => ['url' => 42]]],
            'address only spaces' => [['site' => ['url' => '  ']]],
        ];
    }

    #[Test]
    #[DataProvider('siteWithoutAnAddress')]
    public function hasNoAddressUnlessTheSiteHasOne(mixed $config): void
    {
        static::assertNull(SiteConfig::fromConfig($config)->baseUrl());
    }

    #[Test]
    public function hasNoSettingsOrOptionsWithoutConfig(): void
    {
        $config = SiteConfig::from(new ArrayContainer([]));

        static::assertSame([[], 'social', null], [$config->settings(), $config->options()->block, $config->baseUrl()]);
    }

    #[Test]
    public function ignoresConfigOfTheWrongShape(): void
    {
        $config = SiteConfig::fromConfig('none');

        static::assertSame([[], 'social'], [$config->settings(), $config->options()->block]);
    }

    #[Test]
    public function ignoresLocalSettingsAndOptionsOfTheWrongShape(): void
    {
        $config = SiteConfig::fromConfig([
            'site'            => ['title' => 'Houses'],
            'settings'        => 'none',
            'contenir_social' => ['options' => 'none'],
        ]);

        static::assertSame('social', $config->options()->block);
    }

    #[Test]
    public function ignoresSocialConfigThatIsNotAnArray(): void
    {
        static::assertSame('social', SiteConfig::fromConfig(['contenir_social' => 'none'])->options()->block);
    }

    #[Test]
    public function laysTheLocalSettingsOverTheTopLevelConfig(): void
    {
        $config = SiteConfig::from(new ArrayContainer([
            'config' => [
                'site'     => ['title' => 'Houses', 'url' => 'https://www.example.com'],
                'social'   => ['social_instagram' => 'houses'],
                'settings' => ['site' => ['url' => 'http://houses.localhost']],
            ],
        ]));

        static::assertSame(
            [['title' => 'Houses', 'url' => 'http://houses.localhost'], ['social_instagram' => 'houses']],
            [$config->settings()['site'], $config->settings()['social']],
        );
    }

    #[Test]
    public function readsTheDefaultOptions(): void
    {
        $config = SiteConfig::fromConfig(['contenir_social' => ['options' => ['block' => 'footer']]]);

        static::assertSame('footer', $config->options()->block);
    }

    #[Test]
    public function readsTheSitesAddressTrimmed(): void
    {
        static::assertSame(
            'https://www.example.com',
            SiteConfig::fromConfig(['site' => ['url' => ' https://www.example.com ']])->baseUrl(),
        );
    }

    #[Test]
    public function readsTheTopLevelConfigAsSettingsWithoutLocalOnes(): void
    {
        static::assertSame(['social' => []], SiteConfig::fromConfig(['social' => []])->settings());
    }
}
