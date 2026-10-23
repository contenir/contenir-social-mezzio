<?php

declare(strict_types=1);

namespace Contenir\Social\Mezzio\View\Helper;

use Contenir\Social\Html\Options;
use Contenir\Social\Mezzio\SiteProfiles;
use Contenir\Social\Profiles;

/**
 * `$this->socialProfiles()` in a laminas-view template: the site's profiles
 * as escaped links with rel="me", icons and accessible names.
 *
 *     <?= $this->socialProfiles() ?>
 *     <?= $this->socialProfiles(['block' => 'footer', 'labels' => true]) ?>
 *     <?php $sameAs = $this->plugin('socialProfiles')->sameAs() ?>
 *
 * @api
 */
final readonly class SocialProfiles
{
    public function __construct(
        private SiteProfiles $profiles,
    ) {}

    /**
     * The profiles themselves, for templates that write their own markup.
     */
    public function profiles(): Profiles
    {
        return $this->profiles->profiles();
    }

    /**
     * Every profile's address, for schema.org's sameAs.
     *
     * @return list<string>
     */
    public function sameAs(): array
    {
        return $this->profiles->sameAs();
    }

    /**
     * Every profile's address by its network's name.
     *
     * @return array<string, string>
     */
    public function urls(): array
    {
        return $this->profiles->urls();
    }

    /**
     * The list of links, or an empty string when the site has no profiles.
     *
     * @param Options|array<string, mixed> $options icons, labels, new_tab, block and class
     */
    public function __invoke(Options|array $options = []): string
    {
        return $this->profiles->render($options);
    }
}
