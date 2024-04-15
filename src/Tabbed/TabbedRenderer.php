<?php

/*
 * This file is part of the zenstruck/commonmark-extensions package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\CommonMark\Extension\Tabbed;

use League\CommonMark\Node\Block\Paragraph;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 *
 * @phpstan-type Theme array{
 *     attributes?: array<string,string>,
 *     header?: array{
 *          attributes?: array<string,string>,
 *          tab?: array{
 *              attributes?: array<string,string>,
 *              active_attributes?: array<string,string>,
 *              trigger?: array{
 *                  tag?: string,
 *                  attributes?: array<string,string>,
 *                  active_attributes?: array<string,string>,
 *              },
 *          },
 *     },
 *     body?: array{
 *          attributes?: array<string,string>,
 *          panel?: array{
 *              attributes?: array<string,string>,
 *              active_attributes?: array<string,string>,
 *          },
 *     },
 * }
 */
final class TabbedRenderer implements NodeRendererInterface
{
    /**
     * @param Theme $theme
     */
    public function __construct(private array $theme)
    {
    }

    public function render(Node $node, ChildNodeRendererInterface $childRenderer): string|\Stringable|null
    {
        $firstItemNode = $node->firstChild()?->firstChild()?->firstChild();

        if (!$firstItemNode instanceof Text || !\str_starts_with($firstItemNode->getLiteral(), '===')) {
            return null;
        }

        $tabs = [];
        $panels = [];
        $defaultId = 'tabs-'.\bin2hex(\random_bytes(5));

        foreach ($node->children() as $i => $item) {
            [$title, $id, $children] = self::parseItem($item);
            $tabId = $id ?? $defaultId.'-tab-'.$i;
            $panelId = $id ? $id.'-panel' : $defaultId.'-panel-'.$i;

            $tabAttributes = $this->theme['header']['tab']['attributes'] ?? [];
            $triggerAttributes = $this->theme['header']['tab']['trigger']['attributes'] ?? [];
            $panelAttributes = $this->theme['body']['panel']['attributes'] ?? [];

            if (0 === $i) {
                $tabAttributes = \array_merge($tabAttributes, $this->theme['header']['tab']['active_attributes'] ?? []);
                $triggerAttributes = \array_merge($triggerAttributes, $this->theme['header']['tab']['trigger']['active_attributes'] ?? []);
                $panelAttributes = \array_merge($panelAttributes, $this->theme['body']['panel']['active_attributes'] ?? []);
            }

            $tabAttributes = self::renderIds($tabAttributes, $tabId, $panelId);
            $triggerAttributes = self::renderIds($triggerAttributes, $tabId, $panelId);
            $panelAttributes = self::renderIds($panelAttributes, $tabId, $panelId);

            $tabs[] = new HtmlElement(
                'li',
                $tabAttributes,
                contents: new HtmlElement(
                    $this->theme['header']['tab']['trigger']['tag'] ?? 'button',
                    $triggerAttributes,
                    contents: $title,
                ),
            );
            $panels[] = new HtmlElement(
                'div',
                $panelAttributes,
                contents: $childRenderer->renderNodes($children),
            );
        }

        $header = new HtmlElement(
            'ul',
            $this->theme['header']['attributes'] ?? [],
            contents: \implode("\n", $tabs),
        );
        $content = new HtmlElement(
            'div',
            $this->theme['body']['attributes'] ?? [],
            contents: \implode("\n", $panels),
        );

        return new HtmlElement('div', $this->theme['attributes'] ?? [], contents: $header."\n".$content);
    }

    /**
     * @param array<string,string> $attributes
     *
     * @return array<string,string>
     */
    private static function renderIds(array $attributes, string $tabId, string $panelId): array
    {
        return \array_map(
            static fn(string $value): string => \str_replace(['{tabId}', '{panelId}'], [$tabId, $panelId], $value),
            $attributes
        );
    }

    /**
     * @return array{string,?string,iterable<Node>}
     */
    private static function parseItem(Node $node): array
    {
        $firstChild = $node->firstChild();

        if (!$firstChild instanceof Paragraph) {
            throw new \RuntimeException('Expected first child to be a paragraph.');
        }

        $firstChild->detach();
        $textNode = $firstChild->firstChild();

        if (!$textNode instanceof Text) {
            throw new \RuntimeException('Expected first child of paragraph to be a text node.');
        }

        $text = $textNode->getLiteral();

        if (!\str_starts_with($text, '===')) {
            throw new \RuntimeException('Expected text to start with "===".');
        }

        return [\mb_substr($text, 3), $firstChild->data->get('attributes.id', null), $node->children()];
    }
}
