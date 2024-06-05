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

namespace Yabe\Bricksbender\Modules\ElementSwitch;

use Yabe\Bricksbender\Modules\ModuleInterface;
use Yabe\Bricksbender\Utils\Common;
use Yabe\Bricksbender\Utils\Config;

/**
 * @since 2.0.0
 */
class ElementSwitch implements ModuleInterface
{
    public function __construct()
    {
        if ($this->is_enabled()) {
            if (!Common::is_request('json')) {
                add_filter('bricks/builder/elements', [$this, 'filter_bricks_elements']);
            }
        }
    }

    public function has_setting_page(): bool
    {
        return true;
    }

    public function get_version(): string
    {
        return '1.0.0';
    }

    public function get_name(): string
    {
        return 'element_switch';
    }

    public function get_title(): string
    {
        return 'Element Manager';
    }

    public function get_description(): string
    {
        return 'Manage all elements in the Bricks Builder.';
    }

    public function get_icon(): array
    {
        return [
            'icon' =>  ['fas', 'layer-group',],
        ];
    }

    public function is_enabled(): bool
    {
        return (bool) apply_filters(
            'f!yabe/bricksbender/module/element_switch:enabled',
            Config::get(sprintf(
                'modules.%s.enabled',
                $this->get_name()
            ), true)
        );
    }

    public function filter_bricks_elements(array $element_names): array
    {
        foreach ($element_names as $key => $element_name) {
            if (!Config::get(sprintf('elements.%s.%s.enabled', 'bricks', $element_name), true)) {
                unset($element_names[$key]);
            }
        }

        return $element_names;
    }
}
