<?php

declare(strict_types=1);

namespace Contenir\Social\Mezzio\Tests\Unit;

use Contenir\Social\Exception\InvalidPageException;
use Contenir\Social\Mezzio\SharedPage;
use Contenir\Social\Mezzio\Tests\TestAsset\PageMetadata;
use Contenir\Social\Share\Page;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[Group('unit')]
final class SharedPageTest extends TestCase
{
    private const string BASE = 'https://www.example.com/houses?x=1';

    /**
     * @return list<?string>
     */
    private static function values(Page $page): array
    {
        return [$page->url, $page->title, $page->description, $page->image];
    }

    #[Test]
    public function cannotMakeAnAddressAbsoluteWithoutTheSitesAddress(): void
    {
        $this->expectExceptionObject(InvalidPageException::notAbsolute('address', '/entry/1'));

        SharedPage::from(['url' => '/entry/1']);
    }

    #[Test]
    public function hasNoAddressAtAllWhenNoneIsGiven(): void
    {
        $this->expectExceptionObject(InvalidPageException::notAbsolute('address', ''));

        SharedPage::from(['title' => 'Winners'], self::BASE);
    }

    #[Test]
    public function leavesAProtocolRelativeAddressForPageToRefuse(): void
    {
        $this->expectExceptionObject(InvalidPageException::notAbsolute('address', '//cdn.example.com/x'));

        SharedPage::from(['url' => '//cdn.example.com/x'], self::BASE);
    }

    #[Test]
    public function makesAddressesFromTheRootAbsoluteOnTheSitesHost(): void
    {
        static::assertSame(
            'https://www.example.com/entry/1',
            SharedPage::from(['url' => '/entry/1'], self::BASE)->url,
        );
    }

    #[Test]
    public function makesAddressesFromTheRootAbsoluteOnTheSitesHostAndPort(): void
    {
        static::assertSame(
            ['http://localhost:8080/entry/1', '', '', 'http://localhost:8080/a.jpg'],
            self::values(SharedPage::from(['url' => '/entry/1', 'image' => '/a.jpg'], 'http://localhost:8080/x')),
        );
    }

    #[Test]
    public function readsAnArray(): void
    {
        static::assertSame(
            ['https://www.example.com/1', 'One', 'The first', 'https://www.example.com/1.jpg'],
            self::values(SharedPage::from([
                'url'         => 'https://www.example.com/1',
                'title'       => 'One',
                'description' => 'The first',
                'image'       => 'https://www.example.com/1.jpg',
            ])),
        );
    }

    #[Test]
    public function readsAPagesMetadataByItsProperties(): void
    {
        static::assertSame(
            ['https://www.example.com/1', 'One', 'The first', 'https://www.example.com/1.jpg'],
            self::values(SharedPage::from(new PageMetadata(
                'https://www.example.com/1',
                'One',
                'The first',
                'https://www.example.com/1.jpg',
            ))),
        );
    }

    #[Test]
    public function readsMissingAndUntypedValuesAsEmpty(): void
    {
        static::assertSame(
            ['https://www.example.com/1', '', '', null],
            self::values(SharedPage::from([
                'url'         => 'https://www.example.com/1',
                'title'       => null,
                'description' => 42,
                'image'       => '',
            ])),
        );
    }

    #[Test]
    public function takesAPageAsItIs(): void
    {
        $page = new Page('https://www.example.com/1', 'One');

        static::assertSame($page, SharedPage::from($page, self::BASE));
    }
}
