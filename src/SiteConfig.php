<?php

declare(strict_types=1);

namespace Contenir\Social\Mezzio;

use Contenir\Social\Html\Options;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

use function array_replace_recursive;
use function is_array;
use function is_string;
use function trim;

/**
 * The parts of a Mezzio application's config the services read: the site's
 * settings and the default HTML options (config['contenir_social']['options']).
 *
 * A Mezzio site's settings are its top-level config, where
 * config/autoload/settings.global.php is merged, with config['settings'],
 * where the CMS writes settings.local.php, laid over it.
 *
 * @internal
 */
final readonly class SiteConfig
{
    /**
     * @param array<array-key, mixed> $settings
     */
    private function __construct(
        private array $settings,
        private Options $options,
    ) {}

    /**
     * @throws ContainerExceptionInterface
     */
    public static function from(ContainerInterface $container): self
    {
        return self::fromConfig($container->has('config') ? $container->get('config') : null);
    }

    /**
     * @mago-expect analysis:mixed-assignment The application's config is untyped; each level is checked here.
     */
    public static function fromConfig(mixed $config): self
    {
        $config  = is_array($config) ? $config : [];
        $local   = $config['settings'] ?? null;
        $social  = $config['contenir_social'] ?? null;
        $options = is_array($social) ? $social['options'] ?? null : null;

        return new self(
            is_array($local) ? array_replace_recursive($config, $local) : $config,
            HtmlOptions::from(is_array($options) ? $options : []),
        );
    }

    /**
     * The site's address (settings site.url), which addresses starting with
     * / are made absolute on, or null when the site has none.
     *
     * @mago-expect analysis:mixed-assignment Settings are untyped; each level is checked here.
     */
    public function baseUrl(): ?string
    {
        $site = $this->settings['site'] ?? null;
        $url  = is_array($site) ? $site['url'] ?? null : null;
        $url  = is_string($url) ? trim($url) : '';

        return '' === $url ? null : $url;
    }

    public function options(): Options
    {
        return $this->options;
    }

    /**
     * @return array<array-key, mixed>
     */
    public function settings(): array
    {
        return $this->settings;
    }
}
