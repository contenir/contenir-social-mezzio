<?php

declare(strict_types=1);

namespace Contenir\Social\Mezzio\Tests\Integration;

use Contenir\Social\Mezzio\Tests\TestAsset\PageMetadata;
use Contenir\Social\Mezzio\Tests\TestAsset\Settings;
use Contenir\Social\Mezzio\Tests\Trait\RendersViewsTrait;
use Contenir\Social\Script\ShareScript;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function preg_match;
use function substr_count;

/**
 * A layout calling socialProfiles() and share() through laminas-view's
 * PhpRenderer, with a container built from the package's ConfigProvider and
 * the site's settings as the CMS wrote them.
 */
#[Group('integration')]
final class LayoutTest extends TestCase
{
    use RendersViewsTrait;

    #[Test]
    public function listsTheProfilesForJsonLd(): void
    {
        $this->setUpViews(Settings::global());

        static::assertStringContainsString(
            '{"sameAs":["https://www.facebook.com/housesmagazine","https://www.instagram.com/housesmagazine/",'
                . '"https://x.com/HousesMag"]}',
            $this->render(),
        );
    }

    #[Test]
    public function readsTheLocalSettingsOverTheGlobalOnes(): void
    {
        $this->setUpViews(Settings::withLocal());

        $html = $this->render();

        static::assertSame(
            [0, 1],
            [
                substr_count($html, needle: 'https://x.com/HousesMag"'),
                substr_count(
                    $html,
                    needle: '"sameAs":["https://www.facebook.com/housesmagazine",'
                        . '"https://www.instagram.com/housesmagazine/","https://x.com/HousesLocal"]',
                ),
            ],
        );
    }

    #[Test]
    public function sharesThePageItIsGivenInTheSitesOrder(): void
    {
        $this->setUpViews(Settings::global());

        static::assertSame(
            1,
            preg_match(
                '#<main><ul class="social social--share">'
                    . '<li class="social__item social__item--x"><a class="social__link" href="https://x\.com/intent/tweet\?'
                    . 'text=The%202026%20winners&amp;url=https%3A%2F%2Fwww\.example\.com%2Fwinners".*'
                    . '<li class="social__item social__item--copy" hidden>.*</ul><script>#s',
                $this->render(),
            ),
        );
    }

    #[Test]
    public function usesTheApplicationsDefaultOptions(): void
    {
        $this->setUpViews([
            ...Settings::global(),
            'contenir_social' => ['options' => ['block' => 'site', 'labels' => true]],
        ]);

        $html = $this->render();

        static::assertSame(
            [1, 1, 2, 0],
            [
                substr_count($html, needle: '<ul class="site site--profiles">'),
                substr_count($html, needle: '<ul class="footer footer--profiles">'),
                substr_count($html, needle: '<span class="site__label">X</span>'),
                substr_count($html, needle: 'class="social'),
            ],
        );
    }

    #[Test]
    public function writesTheProfilesTheCoreHeldAsHandles(): void
    {
        $this->setUpViews(Settings::global());

        static::assertSame(
            1,
            preg_match(
                '#<header><ul class="social social--profiles">'
                    . '<li class="social__item social__item--facebook"><a class="social__link" '
                    . 'href="https://www.facebook.com/housesmagazine" rel="me noopener" target="_blank".*'
                    . 'href="https://www.instagram.com/housesmagazine/".*href="https://x.com/HousesMag".*</ul></header>#s',
                $this->render(),
            ),
        );
    }

    #[Test]
    public function writesTheScriptWithTheNonceTheLayoutGives(): void
    {
        $this->setUpViews(Settings::global());

        static::assertSame(1, substr_count($this->render('page-nonce'), ShareScript::tag('page-nonce')));
    }

    private function render(?string $nonce = null): string
    {
        return $this->renderer->render('layout', [
            'metadata' => new PageMetadata('https://www.example.com/winners', 'The 2026 winners'),
            'nonce'    => $nonce,
        ]);
    }
}
