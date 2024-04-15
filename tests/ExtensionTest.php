<?php

/*
 * This file is part of the zenstruck/commonmark-extensions package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\CommonMark\Extension\Tests;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\Attributes\AttributesExtension;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\ExtensionInterface;
use League\CommonMark\MarkdownConverter;
use PHPUnit\Framework\TestCase;
use Zenstruck\CommonMark\Extension\GitHub\AdmonitionExtension;
use Zenstruck\CommonMark\Extension\TabbedExtension;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ExtensionTest extends TestCase
{
    /**
     * @test
     * @dataProvider functionalProvider
     *
     * @param class-string<ExtensionInterface> $extension
     */
    public function functional(string $extension, string $file): void
    {
        $environment = new Environment();
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new AttributesExtension());
        $environment->addExtension(new $extension());

        $converter = new MarkdownConverter($environment);

        [$input, $expected] = \explode('--==--', \file_get_contents(__DIR__.'/Fixtures/'.$file, 2));

        $this->assertSame(\trim($expected), \trim($converter->convert($input)));
    }

    /**
     * @return iterable<array{class-string<ExtensionInterface>, string}>
     */
    public static function functionalProvider(): iterable
    {
        yield 'github_admonition' => [AdmonitionExtension::class, 'github_admonition.test'];
        yield 'tabbed' => [TabbedExtension::class, 'tabbed.test'];
    }
}
