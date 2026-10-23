<?php

declare(strict_types=1);

namespace Contenir\Social\Mezzio\Tests\TestAsset;

use DateTimeInterface;

/**
 * A page's metadata in the shape of contenir-resource's
 * Contenir\Resource\Core\Metadata\PageMetadata, which this package reads by
 * its public properties without requiring contenir-resource.
 */
final readonly class PageMetadata
{
    public function __construct(
        public string $url,
        public ?string $title = null,
        public ?string $description = null,
        public ?string $image = null,
        public ?DateTimeInterface $updated = null,
    ) {}
}
