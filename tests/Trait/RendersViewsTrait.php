<?php

declare(strict_types=1);

namespace Contenir\Social\Mezzio\Tests\Trait;

use Contenir\Social\Mezzio\ConfigProvider;
use Laminas\ServiceManager\ServiceManager;
use Laminas\View\HelperPluginManager;
use Laminas\View\Renderer\PhpRenderer;
use Laminas\View\Resolver\TemplatePathStack;

/**
 * A Mezzio application's container, built from the package's
 * ConfigProvider as laminas-config-aggregator merges it, and a laminas-view
 * renderer whose helpers come from its view_helpers, as
 * mezzio-laminasviewrenderer builds them. Both are built fresh for each test.
 */
trait RendersViewsTrait
{
    private ServiceManager $services;

    private PhpRenderer $renderer;

    /**
     * @param array<string, mixed> $config The application's config
     */
    private function setUpViews(array $config): void
    {
        $provided = (new ConfigProvider())();
        $config   = [...$config, ...$provided];

        $this->services = new ServiceManager([
            ...$provided['dependencies'],
            'services' => ['config' => $config],
        ]);

        $helpers = new HelperPluginManager($this->services, $provided['view_helpers']);

        $this->renderer = new PhpRenderer();
        $this->renderer->setHelperPluginManager($helpers);
        $this->renderer->setResolver(new TemplatePathStack(['script_paths' => [__DIR__ . '/../TestAsset/view']]));
    }
}
