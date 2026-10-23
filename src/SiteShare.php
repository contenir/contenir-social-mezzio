<?php

declare(strict_types=1);

namespace Contenir\Social\Mezzio;

use Contenir\Social\Exception\InvalidPageException;
use Contenir\Social\Html\Options;
use Contenir\Social\Html\ShareButtons;
use Contenir\Social\Script\ShareScript;
use Contenir\Social\Share\Page;
use Contenir\Social\Share\ShareConfig;
use Contenir\Social\Share\ShareLink;
use Contenir\Social\Share\ShareLinks;

use function array_filter;
use function is_array;
use function is_string;

/**
 * A page's share buttons, in the order the site chose in the CMS. The site's
 * share settings are read once; the page and the CSP nonce are given with
 * each call, so one instance serves every request.
 *
 * @api
 */
final readonly class SiteShare
{
    public function __construct(
        private ShareConfig $config,
        private Options $defaults = new Options(),
        private ?string $baseUrl = null,
    ) {}

    /**
     * The site's share buttons and settings.
     */
    public function config(): ShareConfig
    {
        return $this->config;
    }

    /**
     * The page's share links, for markup of a site's own.
     *
     * @param Page|array{url?: ?string, title?: ?string, description?: ?string, image?: ?string}|object $page
     *
     * @return list<ShareLink>
     *
     * @throws InvalidPageException When the page's address isn't absolute and can't be made so.
     */
    public function links(array|object $page): array
    {
        return (new ShareLinks($this->config))->for(SharedPage::from($page, $this->baseUrl));
    }

    /**
     * The share buttons, then, when any is "Copy link" or "Share…", the
     * script behind them, which runs once however often a page includes it.
     *
     * @param Page|array{url?: ?string, title?: ?string, description?: ?string, image?: ?string}|object $page
     * @param Options|array<string, mixed> $options icons, labels, new_tab, block and class, plus script (false
     *                                              leaves the script out) and nonce
     *
     * @throws InvalidPageException When the page's address isn't absolute and can't be made so.
     */
    public function render(array|object $page, Options|array $options = []): string
    {
        $links    = $this->links($page);
        $html     = ShareButtons::render($links, HtmlOptions::from($options, $this->defaults()));
        $settings = is_array($options) ? $options : [];
        $buttons  = array_filter($links, static fn(ShareLink $link): bool => $link->isButton());
        if ([] === $buttons || false === ($settings['script'] ?? true)) {
            return $html;
        }

        return $html . $this->script(is_string($settings['nonce'] ?? null) ? $settings['nonce'] : null);
    }

    /**
     * The script for "Copy link" and "Share…", with the page's CSP nonce
     * when it has one, for a layout that places it itself.
     */
    public function script(?string $nonce = null): string
    {
        return ShareScript::tag($nonce);
    }

    /**
     * The defaults, with names beside icons as the site chose in the CMS
     * unless the application's config says otherwise.
     */
    private function defaults(): Options
    {
        return new Options(
            icons: $this->defaults->icons,
            labels: $this->defaults->labels ?? $this->config->labels,
            newTab: $this->defaults->newTab,
            block: $this->defaults->block,
            class: $this->defaults->class,
        );
    }
}
