<?php

/**
 * Yabe Bricksbender - The Bricks builder extension
 *
 * @wordpress-plugin
 * Plugin Name:         Yabe Bricksbender
 * Plugin URI:          https://bricksbender.yabe.land
 * Description:         The Bricks builder extension
 * Version:             1.0.11
 * Requires at least:   6.0
 * Requires PHP:        7.4
 * Author:              Rosua
 * Author URI:          https://yabe.land
 * Donate link:         https://ko-fi.com/Q5Q75XSF7
 * Text Domain:         yabe-bricksbender
 * Domain Path:         /languages
 * License:             GPL-3.0-or-later
 *
 * @package             Yabe
 * @author              Joshua Gugun Siagian <suabahasa@gmail.com>
 */

declare(strict_types=1);

use Yabe\Bricksbender\Plugin;
use Yabe\Bricksbender\Utils\Requirement;

defined('ABSPATH') || exit;

if (file_exists(__DIR__ . '/vendor/scoper-autoload.php')) {
    require_once __DIR__ . '/vendor/scoper-autoload.php';
} else {
    require_once __DIR__ . '/vendor/autoload.php';
}

$requirement = new Requirement();
$requirement
    ->php('7.4')
    ->wp('6.0')
    ->theme('bricks', '1.9.6.1');

if ($requirement->met()) {
    Plugin::get_instance()->boot();
} else {
    add_action('admin_notices', fn () => $requirement->printNotice(), 0, 0);
}
