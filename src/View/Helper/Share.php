<?php

declare(strict_types=1);

namespace Contenir\Social\Mezzio\View\Helper;

use Contenir\Social\Exception\InvalidPageException;
use Contenir\Social\Html\Options;
use Contenir\Social\Mezzio\SiteShare;
use Contenir\Social\Share\Page;
use Contenir\Social\Share\ShareLink;

/**
 * `$this->share($page)` in a laminas-view template: the page's share
 * buttons, in the order the site chose in the CMS, and the script behind
 * "Copy link" and "Share…" when they are among them. The page is given: a
 * Share\Page, contenir-resource's PageMetadata, or an array with url, title,
 * description and image.
 *
 *     <?= $this->share($metadata) ?>
 *     <?= $this->share(['url' => $entry->url, 'title' => $entry->title], ['nonce' => $nonce]) ?>
 *
 * @api
 */
final readonly class Share
{
    public function __construct(
        private SiteShare $share,
    ) {}

    /**
     * The page's share links, for templates that write their own markup.
     *
     * @param Page|array{url?: ?string, title?: ?string, description?: ?string, image?: ?string}|object $page
     *
     * @return list<ShareLink>
     *
     * @throws InvalidPageException When the page's address isn't absolute and can't be made so.
     */
    public function links(array|object $page): array
    {
        return $this->share->links($page);
    }

    /**
     * The script for "Copy link" and "Share…", for a layout that leaves it
     * out of share() and places it itself.
     */
    public function script(?string $nonce = null): string
    {
        return $this->share->script($nonce);
    }

    /**
     * The share buttons, then the script when the page needs it.
     *
     * @param Page|array{url?: ?string, title?: ?string, description?: ?string, image?: ?string}|object $page
     * @param Options|array<string, mixed> $options icons, labels, new_tab, block and class, plus script (false
     *                                              leaves the script out) and nonce
     *
     * @throws InvalidPageException When the page's address isn't absolute and can't be made so.
     */
    public function __invoke(array|object $page, Options|array $options = []): string
    {
        return $this->share->render($page, $options);
    }
}
