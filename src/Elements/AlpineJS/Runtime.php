<?php

/*
 * This file is part of the Yabe package.
 *
 * (c) Joshua Gugun Siagian <suabahasa@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Yabe\Bricksbender\Elements\AlpineJS;

use Bricks\Element;
use Yabe\Bricksbender\Elements\ElementInterface;
use Yabe\Bricksbender\Utils\AssetVite;
use Yabe\Bricksbender\Utils\Common;

/**
 * @since 1.0.0
 */
class Runtime extends Element implements ElementInterface
{
    /**
     * @var string
     */
    public $category = 'Bricksbender — Alpine.js';

    /**
     * @var string
     */
    public $name = 'ybr_alpinejs_runtime';

    /**
     * @var string
     */
    public $icon = 'ti ti-brand-alpine-js';

    public $scripts = ['ybrAlpinejsRuntime'];

    private $official_plugins = [
        'mask' => '@alpinejs/mask',
        'intersect' => '@alpinejs/intersect',
        'persist' => '@alpinejs/persist',
        'focus' => '@alpinejs/focus',
        'collapse' => '@alpinejs/collapse',
        'anchor' => '@alpinejs/anchor',
        'morph' => '@alpinejs/morph',
        'sort' => '@alpinejs/sort',
    ];

    public function get_identifier(): string
    {
        return 'ybr_alpinejs_runtime';
    }

    public function get_label()
    {
        return esc_html__('Alpine.js Runtime', 'yabe-bricksbender');
    }

    public function set_control_groups()
    {
        $this->control_groups['official_plugins'] = [
            'title' => esc_html__('Official Plugins', 'yabe-bricksbender'),
            'tab' => 'content',
        ];
    }

    private function get_runtime_assets()
    {
        $assets = [];

        // Official plugins
        foreach ($this->official_plugins as $plugin => $package) {
            $setting_name = 'enablePlugin' . ucfirst($plugin);
            if (isset($this->settings[$setting_name]) && $this->settings[$setting_name] === true) {
                $plugin_version = $this->settings['selectPlugin' . ucfirst($plugin) . 'Version'] ?? 'latest';
                $assets[] = [
                    'handle' => 'ybr-alpinejs-' . $plugin,
                    'src' => 'https://cdn.jsdelivr.net/npm/' . $package . '@' . $plugin_version . '/dist/cdn.min.js',
                    'version' => $plugin_version,
                ];
            }
        }

        // Core
        $core_version = $this->settings['selectCoreVersion'] ?? 'latest';
        $assets[] = [
            'handle' => 'ybr-alpinejs-core',
            'src' => 'https://cdn.jsdelivr.net/npm/alpinejs@' . $core_version . '/dist/cdn.min.js',
            'version' => $core_version,
        ];

        return $assets;
    }

    public function enqueue_scripts()
    {
        if (bricks_is_builder_iframe()) {
            AssetVite::get_instance()->enqueue_asset('assets/elements/alpinejs/runtime.js', [
                'handle' => 'ybr-alpinejs-runtime',
                'in_footer' => true,
            ]);
        } else {
            foreach ($this->get_runtime_assets() as $asset) {
                wp_enqueue_script($asset['handle'], $asset['src'], [], null, ['strategy' => 'defer']);
            }
        }
    }

    public function render()
    {
        if (bricks_is_builder() || Common::is_request('rest') || Common::is_request('ajax')) {
            $this->set_attribute(
                '_root',
                'data-ybr-alpinejs-runtime-options',
                wp_json_encode(
                    [
                        'assets' => $this->get_runtime_assets(),
                    ]
                )
            );

            echo "<div {$this->render_attributes('_root')}></div>";
        }
    }

    public function set_controls()
    {
        $npm_versions = $this->npm_versions();

        $this->controls['selectCoreVersion'] = [
            'tab' => 'content',
            'label' => esc_html__('Core Version', 'yabe-bricksbender'),
            'type' => 'select',
            'options' => array_combine($npm_versions['core'], $npm_versions['core']),
            'inline' => false,
            'placeholder' => esc_html__('latest', 'yabe-bricksbender'),
            'multiple' => false,
            'searchable' => true,
            'clearable' => true,
            'description' => sprintf(
                '<a href="https://alpinejs.dev/" target="_blank" rel="noopener">%s</a>',
                esc_html__('Learn more about Alpine.js', 'yabe-bricksbender')
            ),
        ];

        if (count($npm_versions['core']) > 0) {
            $this->controls['selectCoreVersionInfo'] = [
                'tab' => 'content',
                'content' => sprintf(esc_html__('The latest version is %s', 'yabe-bricksbender'), $npm_versions['core'][1]),
                'type' => 'info',
                'required' => [
                    ['selectCoreVersion', '=', ['latest', '']],
                ],
            ];
        }

        // Official plugins
        foreach ($this->official_plugins as $plugin => $package) {
            // enable the plugin
            $this->controls['enablePlugin' . ucfirst($plugin)] = [
                'label' => ucfirst($plugin),
                'type' => 'checkbox',
                'inline' => true,
                'small' => true,
                'group' => 'official_plugins',
            ];

            $this->controls['selectPlugin' . ucfirst($plugin) . 'Version'] = [
                'tab' => 'content',
                'type' => 'select',
                'options' => array_combine($npm_versions['plugin_' . $plugin], $npm_versions['plugin_' . $plugin]),
                'inline' => false,
                'placeholder' => esc_html__('latest', 'yabe-bricksbender'),
                'multiple' => false,
                'searchable' => true,
                'clearable' => true,
                'group' => 'official_plugins',
                'description' => sprintf(
                    '<a href="https://alpinejs.dev/plugins/%s" target="_blank" rel="noopener">%s</a>',
                    $plugin,
                    sprintf(esc_html__('Learn more about the Alpine\'s %s plugin', 'yabe-bricksbender'), ucfirst($plugin))
                ),
                'required' => ['enablePlugin' . ucfirst($plugin), '=', true]
            ];

            if (count($npm_versions['plugin_' . $plugin]) > 0) {
                $this->controls['selectPlugin' . ucfirst($plugin) . 'VersionInfo'] = [
                    'tab' => 'content',
                    'content' => sprintf(esc_html__('The latest version is %s', 'yabe-bricksbender'), $npm_versions['plugin_' . $plugin][1]),
                    'type' => 'info',
                    'group' => 'official_plugins',
                    'required' => [
                        ['enablePlugin' . ucfirst($plugin), '=', true],
                        ['selectPlugin' . ucfirst($plugin) . 'Version', '=', ['latest', '']],
                    ]
                ];
            }
        }
    }

    public function npm_versions(): array
    {
        // Get cached versions
        $transient_name = 'bricksbender_element_alpinejs_versions';

        /** @var array $cached */
        $cached = get_transient($transient_name);

        // if (!WP_DEBUG && $cached !== false) {
        //     return $cached;
        // }

        $versions = array_merge(
            ['core' => $this->fetch_core_version()],
            $this->fetch_official_plugins_versions()
        );

        set_transient($transient_name, $versions, HOUR_IN_SECONDS);

        return $versions;
    }

    private function fetch_core_version(): array
    {
        $url = 'https://data.jsdelivr.com/v1/package/npm/alpinejs';
        $response = wp_remote_get($url);

        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
            return ['latest'];
        }

        $body = wp_remote_retrieve_body($response);

        if (is_wp_error($body)) {
            return ['latest'];
        }

        $data = json_decode($body, true);

        // filter versions that greater than 3.0.0
        $versions = array_filter($data['versions'], function ($v) {
            return version_compare($v, '3.0.0', '>=');
        });


        return ['latest', ...$versions];
    }

    private function fetch_official_plugins_versions(): array
    {
        $versions = [];

        foreach ($this->official_plugins as $plugin => $package) {
            $response = wp_remote_get('https://data.jsdelivr.com/v1/package/npm/' . $package);

            if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
                continue;
            }

            $body = wp_remote_retrieve_body($response);

            if (is_wp_error($body)) {
                continue;
            }

            $data = json_decode($body, true);

            $versions['plugin_' . $plugin] = ['latest', ...$data['versions']];
        }

        return $versions;
    }
}
