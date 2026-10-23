<?php

declare(strict_types=1);

namespace Contenir\Social\Mezzio;

use Contenir\Social\Html\Options;

use function is_bool;
use function is_string;

/**
 * Html\Options from the arrays templates and config pass: icons, labels,
 * new_tab, block and class. Anything not given keeps its default.
 *
 * @api
 */
final class HtmlOptions
{
    /**
     * $options over $defaults.
     *
     * @param Options|array<array-key, mixed> $options
     *
     * @mago-expect analysis:mixed-assignment Options from templates and config are untyped; each is checked here.
     */
    public static function from(Options|array $options, Options $defaults = new Options()): Options
    {
        if ($options instanceof Options) {
            return $options;
        }

        $labels = $options['labels'] ?? $defaults->labels;

        return new Options(
            icons: self::flag($options['icons'] ?? null, $defaults->icons),
            labels: is_bool($labels) ? $labels : $defaults->labels,
            newTab: self::flag($options['new_tab'] ?? null, $defaults->newTab),
            block: self::text($options['block'] ?? null, $defaults->block),
            class: self::text($options['class'] ?? null, $defaults->class),
        );
    }

    private static function flag(mixed $value, bool $default): bool
    {
        return is_bool($value) ? $value : $default;
    }

    private static function text(mixed $value, string $default): string
    {
        return is_string($value) ? $value : $default;
    }
}
