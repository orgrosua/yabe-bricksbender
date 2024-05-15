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

namespace Yabe\Bricksbender\Elements\Rive;

use Bricks\Element;
use Yabe\Bricksbender\Elements\ElementInterface;

/**
 * @since 1.0.0
 */
class Rive extends Element implements ElementInterface
{
    /**
     * @var string
     */
    public $category = 'Bricksbender — Animation';

    /**
     * @var string
     */
    public $name = 'ybr_rive';

    /**
     * @var string
     */
    public $icon = 'ybr ybr-editor ybr-element-rive';

    public function get_identifier(): string
    {
        return 'ybr_rive';
    }

    public function get_label()
    {
        return esc_html__('Rive', 'yabe-bricksbender');
    }
}
