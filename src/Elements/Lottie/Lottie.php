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

/**
 * @since 1.0.0
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
}
