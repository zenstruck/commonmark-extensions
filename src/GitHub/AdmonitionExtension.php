<?php

/*
 * This file is part of the zenstruck/commonmark-extensions package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\CommonMark\Extension\GitHub;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\CommonMark\Node\Block\BlockQuote;
use League\CommonMark\Extension\ExtensionInterface;
use Zenstruck\CommonMark\Extension\GitHub\Admonition\AdmonitionRenderer;

/**
 * GitHub-flavored admonitions.
 *
 * @see https://github.com/orgs/community/discussions/16925
 *
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class AdmonitionExtension implements ExtensionInterface
{
    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment->addRenderer(BlockQuote::class, new AdmonitionRenderer(), priority: 10);
    }
}
