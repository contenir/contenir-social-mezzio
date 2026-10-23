<?php

declare(strict_types=1);

namespace Contenir\Social\Mezzio\Tests\Unit;

use Contenir\Social\Html\Options;
use Contenir\Social\Mezzio\HtmlOptions;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
final class HtmlOptionsTest extends TestCase
{
    /**
     * @return list<mixed>
     */
    private static function values(Options $options): array
    {
        return [$options->icons, $options->labels, $options->newTab, $options->block, $options->class];
    }

    #[Test]
    public function keepsTheDefaultsForValuesOfTheWrongType(): void
    {
        static::assertSame(
            [true, null, true, 'social', ''],
            self::values(HtmlOptions::from([
                'icons'   => 'no',
                'labels'  => 1,
                'new_tab' => 0,
                'block'   => ['footer'],
                'class'   => false,
            ])),
        );
    }

    #[Test]
    public function keepsTheDefaultsForWhatIsNotGiven(): void
    {
        static::assertSame(
            [false, false, false, 'nav', 'x'],
            self::values(HtmlOptions::from([], new Options(false, false, false, 'nav', 'x'))),
        );
    }

    #[Test]
    public function readsEveryOptionFromAnArray(): void
    {
        static::assertSame(
            [false, true, false, 'footer', 'footer__social'],
            self::values(HtmlOptions::from([
                'icons'   => false,
                'labels'  => true,
                'new_tab' => false,
                'block'   => 'footer',
                'class'   => 'footer__social',
            ])),
        );
    }

    #[Test]
    public function takesOptionsAsTheyAre(): void
    {
        $options = new Options(block: 'nav');

        static::assertSame($options, HtmlOptions::from($options, new Options(block: 'other')));
    }
}
