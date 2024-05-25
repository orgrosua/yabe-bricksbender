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

namespace Yabe\Bricksbender\Modules\ElementManager;

use Yabe\Bricksbender\Modules\ModuleInterface;

/**
 * @since 2.0.0
 */
class Manager implements ModuleInterface
{
    public function get_name(): string {
        return 'element_switch';
    }
}
