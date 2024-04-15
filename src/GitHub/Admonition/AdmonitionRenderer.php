<?php

/*
 * This file is part of the zenstruck/commonmark-extensions package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\CommonMark\Extension\GitHub\Admonition;

use League\CommonMark\Extension\CommonMark\Node\Block\BlockQuote;
use League\CommonMark\Extension\CommonMark\Renderer\Block\BlockQuoteRenderer;
use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
final class AdmonitionRenderer implements NodeRendererInterface
{
    private const TYPES = [
        'NOTE',
        'TIP',
        'IMPORTANT',
        'WARNING',
        'CAUTION',
    ];

    private BlockQuoteRenderer $baseRenderer;

    public function __construct()
    {
        $this->baseRenderer = new BlockQuoteRenderer();
    }

    /**
     * @param BlockQuote $node
     */
    public function render(Node $node, ChildNodeRendererInterface $childRenderer): string|\Stringable|null
    {
        if (!$parsed = self::parseBlockQuote($node)) {
            return null;
        }

        [$textNode, $type] = $parsed;

        $textNode->detach();

        $p = new Paragraph();
        $p->data->set('attributes', ['class' => 'md-admonition-label']);
        $p->appendChild(new Text(\ucfirst($type)));

        $node->prependChild($p);
        $node->data->set('attributes', ['class' => "md-admonition md-admonition-{$type}", 'role' => 'alert']);

        return $this->baseRenderer->render($node, $childRenderer);
    }

    /**
     * @return array{Text,string}|null
     */
    private static function parseBlockQuote(Node $node): ?array
    {
        $textNode = $node->firstChild()?->firstChild();

        if (!$textNode instanceof Text || !\preg_match('#^\[!([A-Z]+)]$#', $textNode->getLiteral(), $matches)) {
            return null;
        }

        $type = $matches[1];

        if (!\in_array($type, self::TYPES, true)) {
            return null;
        }

        return [$textNode, \mb_strtolower($type)];
    }
}
