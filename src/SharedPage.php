<?php

declare(strict_types=1);

namespace Contenir\Social\Mezzio;

use Contenir\Social\Exception\InvalidPageException;
use Contenir\Social\Share\Page;

use function get_object_vars;
use function is_array;
use function is_string;
use function parse_url;
use function str_starts_with;

use const PHP_URL_HOST;
use const PHP_URL_PORT;
use const PHP_URL_SCHEME;

/**
 * The page being shared, as a template gives it: a Share\Page, an array
 * with url, title, description and image, or any object with those public
 * properties, such as contenir-resource's PageMetadata. Missing or null
 * values are empty; addresses starting with / are made absolute on the
 * site's address.
 *
 * @api
 */
final class SharedPage
{
    /**
     * @param Page|array{url?: ?string, title?: ?string, description?: ?string, image?: ?string}|object $page
     *
     * @throws InvalidPageException When the address, or the image's, isn't absolute and can't be made so.
     */
    public static function from(array|object $page, ?string $baseUrl = null): Page
    {
        if ($page instanceof Page) {
            return $page;
        }

        $values = is_array($page) ? $page : get_object_vars($page);
        $image  = self::text($values['image'] ?? null);

        return new Page(
            self::absolute(self::text($values['url'] ?? null), $baseUrl),
            self::text($values['title'] ?? null),
            self::text($values['description'] ?? null),
            '' === $image ? null : self::absolute($image, $baseUrl),
        );
    }

    /**
     * $url, made absolute on $baseUrl's scheme, host and port when it starts
     * with a single /.
     */
    private static function absolute(string $url, ?string $baseUrl): string
    {
        if (null === $baseUrl || ! str_starts_with($url, '/') || str_starts_with($url, '//')) {
            return $url;
        }

        $scheme = parse_url($baseUrl, PHP_URL_SCHEME);
        $host   = parse_url($baseUrl, PHP_URL_HOST);
        $port   = parse_url($baseUrl, PHP_URL_PORT);

        return "{$scheme}://{$host}" . (null === $port ? '' : ":{$port}") . $url;
    }

    private static function text(mixed $value): string
    {
        return is_string($value) ? $value : '';
    }
}
