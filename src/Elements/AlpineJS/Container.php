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

use Bricks\Element_Container;
use Bricks\Frontend;
use Yabe\Bricksbender\Elements\ElementInterface;

/**
 * @since 2.0.0
 * 
 * @see https://academy.bricksbuilder.io/article/nestable-elements/
 */
class Container extends Element_Container implements ElementInterface
{
    /**
     * @var string
     */
    public $category = 'Bricksbender — Alpine.js';

    /**
     * @var string
     */
    public $name = 'ybr_alpinejs_container';

    /**
     * @var string
     */
    public $icon = 'ti ti-brand-alpine-js';

    /**
     * @var array
     */
    public $scripts = [];

    /**
     * @var ?string
     */
    public $vue_component = null;

    /**
     * @var bool
     */
    public $nestable = true;

    public function get_identifier(): string
    {
        return 'ybr_alpinejs_container';
    }

    public function get_label()
    {
        return esc_html__('Alpine.js Container', 'yabe-bricksbender');
    }

    public function render()
    {
        $output = "<div {$this->render_attributes('_root')}>";

        // Render children elements (= individual items)
        $output .= Frontend::render_children($this);

        $output .= '</div>';

        echo $output;
    }

    public static function render_builder()
    {
?>
        <script type="text/x-template" id="tmpl-bricks-element-ybr_alpinejs_container">
            <component :is="tag" v-bind="{...(settings._attributes ? Object.assign({}, ...settings._attributes.map(attr => ({ [attr.name]: attr.value ?? '' }))) : {})}">
                <bricks-element-children :element="element"/>
            </component>
        </script>
<?php
    }
}