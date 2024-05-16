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

namespace Yabe\Bricksbender\Elements\Lottie;

use Bricks\Element;
use Yabe\Bricksbender\Elements\ElementInterface;
use Yabe\Bricksbender\Utils\AssetVite;

/**
 * @since 2.0.0
 */
class Lottie extends Element implements ElementInterface
{
    /**
     * @var string
     */
    public $category = 'Bricksbender — Animation';

    /**
     * @var string
     */
    public $name = 'ybr_lottie';

    /**
     * @var string
     */
    public $icon = 'ybr ybr-editor ybr-element-lottie';

    public function get_identifier(): string
    {
        return 'ybr_lottie';
    }

    public function get_label()
    {
        return esc_html__('Lottie', 'yabe-bricksbender');
    }

    public function enqueue_scripts()
    {
        wp_enqueue_script_module('ybr-lottie-player', 'https://cdn.jsdelivr.net/npm/@lottiefiles/dotlottie-wc@latest/dist/dotlottie-wc.js', [], null);

        AssetVite::get_instance()->enqueue_asset('assets/elements/lottie/main.js', [
            'handle' => 'ybr-lottie-main',
            'in_footer' => true,
        ]);
    }

    public function render()
    {
        $source = $this->settings['source'] ?? 'file';

        if ($source === 'file' && !empty($this->settings['file']['id'])) {
            $this->set_attribute(
                '_root',
                'src',
                wp_get_attachment_url($this->settings['file']['id'])
            );
        } else {
            $this->set_attribute(
                '_root',
                'src',
                $this->settings['external'] ?? 'https://assets-v2.lottiefiles.com/a/e25360fe-1150-11ee-9d43-2f8655b815bb/xSk6HtgPaN.lottie'
            );
        }

        // autoplay
        if (!isset($this->settings['trigger']) || $this->settings['trigger'] === 'autoplay') {
            $this->set_attribute(
                '_root',
                'autoplay',
                'true'
            );
        }

        // loop
        if ($this->settings['loop'] ?? false) {
            $this->set_attribute(
                '_root',
                'loop',
                'true'
            );
        }

        // speed
        $this->set_attribute(
            '_root',
            'speed',
            $this->settings['speed'] ?? 1
        );

        // mode
        $this->set_attribute(
            '_root',
            'mode',
            $this->settings['mode'] ?? 'forward'
        );

        // available settings
        $this->set_attribute(
            '_root',
            'data-ybr-lottie-settings',
            wp_json_encode($this->settings)
        );

        echo "<dotlottie-wc {$this->render_attributes('_root')}></dotlottie-wc>";
    }

    public function set_controls()
    {
        $this->controls['source'] = [
            'label' => esc_html__('Source', 'yabe-bricksbender'),
            'type' => 'select',
            'placeholder' => esc_html__('External URL', 'yabe-bricksbender'),
            'inline' => true,
            'options' => [
                'external' => esc_html__('External URL', 'yabe-bricksbender'),
                'file' => esc_html__('File', 'yabe-bricksbender'),
            ],
        ];

        $this->controls['file'] = [
            'type' => 'file',
            'required' => ['source', '=', 'file'],
            'description' => esc_html__('The .json or .lottie file.', 'yabe-bricksbender'),
        ];

        $this->controls['external'] = [
            'tab' => 'content',
            'type' => 'text',
            'required' => ['source', '=', ['', 'external']],
            'default' => 'https://assets-v2.lottiefiles.com/a/e25360fe-1150-11ee-9d43-2f8655b815bb/xSk6HtgPaN.lottie',
            'placeholder' => 'https://assets-v2.lottiefiles.com/a/e25360fe-1150-11ee-9d43-2f8655b815bb/xSk6HtgPaN.lottie',
            'description' => esc_html__('The URL of the .json or .lottie file.', 'yabe-bricksbender'),
        ];

        $this->controls['trigger'] = [
            'label' => esc_html__('Trigger', 'yabe-bricksbender'),
            'type' => 'select',
            'placeholder' => esc_html__('Autoplay', 'yabe-bricksbender'),
            'inline' => true,
            'options' => [
                'autoplay' => esc_html__('Autoplay', 'yabe-bricksbender'),
                'click' => esc_html__('Click', 'yabe-bricksbender'),
                'hover' => esc_html__('Hover', 'yabe-bricksbender'),
                'scroll' => esc_html__('Scroll', 'yabe-bricksbender'),
                'viewport' => esc_html__('Viewport', 'yabe-bricksbender'),
                'none' => esc_html__('None', 'yabe-bricksbender'),
            ],
        ];

        $this->controls['hover_out'] = [
            'label' => esc_html__('Mouseout action', 'yabe-bricksbender'),
            'type' => 'select',
            'placeholder' => esc_html__('No Action', 'yabe-bricksbender'),
            'inline' => true,
            'options' => [
                'noaction' => esc_html__('No Action', 'yabe-bricksbender'),
                'pause' => esc_html__('Pause', 'yabe-bricksbender'),
                'reverse' => esc_html__('Reverse', 'yabe-bricksbender'),
                'stop' => esc_html__('Stop', 'yabe-bricksbender'),
            ],
            'required' => ['trigger', '=', 'hover'],
        ];

        // $this->controls['scroll_selector'] = [
        //     'label' => esc_html__('Scroll Selector', 'yabe-bricksbender'),
        //     'type' => 'text',
        //     'inline' => true,
        //     'placeholder' => esc_html__('body', 'yabe-bricksbender'),
        //     'required' => ['trigger', '=', 'scroll'],
        //     'description' => esc_html__('The scroll are relative to this element selector', 'yabe-bricksbender'),
        // ];

        // $this->controls['scroll_offset_top'] = [
        //     'label' => esc_html__('Offset Top (%)', 'yabe-bricksbender'),
        //     'type' => 'number',
        //     'inline' => true,
        //     'placeholder' => '100',
        //     'required' => [
        //         ['scroll_selector', '!=', ['', 'body']],
        //         ['trigger', '=', 'scroll'],
        //     ],
        //     'description' => esc_html__('Distance from the top of the viewport for animation to end. Must be greater than the Offset Bottom value', 'yabe-bricksbender'),
        // ];

        // $this->controls['scroll_offset_bottom'] = [
        //     'label' => esc_html__('Offset Bottom (%)', 'yabe-bricksbender'),
        //     'type' => 'number',
        //     'inline' => true,
        //     'placeholder' => '0',
        //     'required' => [
        //         ['scroll_selector', '!=', ['', 'body']],
        //         ['trigger', '=', 'scroll'],
        //     ],
        //     'description' => esc_html__('Distance from the bottom of the viewport for animation to start. Must be less than the Offset Top value', 'yabe-bricksbender'),
        // ];

        $this->controls['mode'] = [
            'label' => esc_html__('Mode', 'yabe-bricksbender'),
            'type' => 'select',
            'placeholder' => esc_html__('Forward', 'yabe-bricksbender'),
            'inline' => true,
            'options' => [
                'forward' => esc_html__('Forward', 'yabe-bricksbender'),
                'reverse' => esc_html__('Reverse', 'yabe-bricksbender'),
                'bounce' => esc_html__('Bounce', 'yabe-bricksbender'),
                'reverse-bounce' => esc_html__('Reverse Bounce', 'yabe-bricksbender'),
            ],
            'description' => esc_html__('Animation play mode.', 'yabe-bricksbender'),
        ];

        $this->controls['loop'] = [
            'label' => esc_html__('Loop', 'yabe-bricksbender'),
            'type' => 'checkbox',
            'inline' => true,
            'default' => true,
            'description' => esc_html__('Determines if the animation should loop.', 'yabe-bricksbender'),
        ];

        $this->controls['speed'] = [
            'label' => esc_html__('Animation Speed', 'yabe-bricksbender'),
            'type' => 'number',
            'inline' => true,
            'placeholder' => '1',
            'description' => esc_html__('Animation playback speed. 1 is regular speed.', 'yabe-bricksbender'),
        ];
    }
}
