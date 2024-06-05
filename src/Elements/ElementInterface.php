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

namespace Yabe\Bricksbender\Elements;

interface ElementInterface
{
    /**
     * Get the Element's identifier.
     * 
     * This identifier will be used on the associated configuration and to register the Element.
     * The value should be unique and URL-friendly.
     */
    public function get_identifier(): string;

    /**
     * Get the Element's label.
     * 
     * This label will be used on the Element's UI.
     */
    public function get_label();
}
