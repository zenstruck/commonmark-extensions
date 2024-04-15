<?php

/*
 * This file is part of the zenstruck/commonmark-extensions package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\CommonMark\Extension;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\CommonMark\Node\Block\ListBlock;
use League\CommonMark\Extension\ExtensionInterface;
use Zenstruck\CommonMark\Extension\Tabbed\TabbedRenderer;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @phpstan-import-type Theme from TabbedRenderer
 */
final class TabbedExtension implements ExtensionInterface
{
    public const THEMES = [
        'default' => [
            'attributes' => ['class' => 'md-tabbed'], // tabbed container div attributes
            'header' => [
                'attributes' => ['class' => 'md-tabbed-tabs', 'role' => 'tablist'], // tab header ul attributes
                'tab' => [
                    'attributes' => ['class' => 'md-tabbed-tab', 'role' => 'presentation'], // tab li attributes
                    'active_attributes' => [], // active tab li attributes
                    'trigger' => [
                        'tag' => 'button', // tab trigger tag
                        'attributes' => ['id' => '{tabId}', 'class' => 'md-tabbed-tab-trigger', 'type' => 'button', 'role' => 'tab', 'aria-selected' => 'false', 'aria-controls' => '{panelId}'], // tab trigger attributes
                        'active_attributes' => ['class' => 'md-tabbed-tab-trigger active', 'aria-selected' => 'true'], // active tab trigger attributes
                    ],
                ],
            ],
            'body' => [
                'attributes' => ['class' => 'md-tabbed-panels'], // panel container div attributes
                'panel' => [
                    'attributes' => ['id' => '{panelId}', 'class' => 'md-tabbed-panel', 'role' => 'tabpanel', 'tabindex' => '0', 'aria-labelledby' => '{tabId}'], // panel div attributes
                    'active_attributes' => ['class' => 'md-tabbed-panel active'], // active panel div attributes
                ],
            ],
        ],
        'bootstrap' => [
            'header' => [
                'attributes' => ['class' => 'nav nav-tabs', 'role' => 'tablist'],
                'tab' => [
                    'attributes' => ['class' => 'nav-item', 'role' => 'presentation'],
                    'trigger' => [
                        'tag' => 'button',
                        'attributes' => ['id' => '{tabId}', 'class' => 'nav-link', 'type' => 'button', 'role' => 'tab', 'aria-selected' => 'false', 'data-bs-toggle' => 'tab', 'data-bs-target' => '#{panelId}', 'aria-controls' => '{panelId}'],
                        'active_attributes' => ['class' => 'nav-link active', 'aria-selected' => 'true'],
                    ],
                ],
            ],
            'body' => [
                'attributes' => ['class' => 'tab-content'],
                'panel' => [
                    'attributes' => ['id' => '{panelId}', 'class' => 'tab-pane fade', 'role' => 'tabpanel', 'tabindex' => '0', 'aria-labelledby' => '{tabId}'],
                    'active_attributes' => ['class' => 'tab-pane fade show active'],
                ],
            ],
        ],
    ];

    /**
     * @param Theme $theme
     */
    public function __construct(private array $theme = self::THEMES['default'])
    {
    }

    public static function bootstrapTheme(): self
    {
        return new self(self::THEMES['bootstrap']);
    }

    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment->addRenderer(ListBlock::class, new TabbedRenderer($this->theme), priority: 10);
    }
}
