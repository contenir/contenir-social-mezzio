<?php

declare(strict_types=1);

namespace Contenir\Social\Mezzio;

use Contenir\Social\Html\Options;
use Contenir\Social\Html\ProfileLinks;
use Contenir\Social\Profiles;

/**
 * The site's profiles, read once from its settings: as escaped links with
 * rel="me", icons and accessible names, or as data for JSON-LD and markup
 * of a site's own. It holds nothing about a request, so one instance serves
 * every page.
 *
 * @api
 */
final readonly class SiteProfiles
{
    public function __construct(
        private Profiles $profiles,
        private Options $defaults = new Options(),
    ) {}

    /**
     * The profiles themselves: all(), get(Network), sameAs() and twitterSite().
     */
    public function profiles(): Profiles
    {
        return $this->profiles;
    }

    /**
     * The list of links, or an empty string when the site has no profiles.
     *
     * @param Options|array<string, mixed> $options icons, labels, new_tab, block and class, over the defaults
     */
    public function render(Options|array $options = []): string
    {
        return ProfileLinks::render($this->profiles, HtmlOptions::from($options, $this->defaults));
    }

    /**
     * Every profile's address, in the order sites list them, for
     * schema.org's sameAs.
     *
     * @return list<string>
     */
    public function sameAs(): array
    {
        return $this->profiles->sameAs();
    }

    /**
     * Every profile's address by its network's name (instagram, linkedin…),
     * in the order sites list them.
     *
     * @return array<string, string>
     */
    public function urls(): array
    {
        $urls = [];
        foreach ($this->profiles->all() as $profile) {
            $urls[$profile->network->value] = $profile->url;
        }

        return $urls;
    }
}
