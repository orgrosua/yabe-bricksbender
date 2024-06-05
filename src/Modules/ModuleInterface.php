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

namespace Yabe\Bricksbender\Modules;

interface ModuleInterface
{
    /**
     * Get the Module name (slug).
     */
    public function get_name(): string;

    /**
     * Get the Module title.
     */
    public function get_title(): string;

    /**
     * Get the Module description.
     */
    public function get_description(): string;

    /**
     * Get the Module icon.
     */
    public function get_icon(): array;

    /**
     * Is module enabled.
     */
    public function is_enabled(): bool;

    /**
     * Module version.
     */
    public function get_version(): string;

    /**
     * Module has setting page.
     */
    public function has_setting_page(): bool;
}
