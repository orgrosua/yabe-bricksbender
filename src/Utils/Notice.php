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

namespace Yabe\Bricksbender\Utils;

use BRICKSBENDER;

/**
 * WordPress admin notice manager.
 *
 * @since 1.0.0
 *
 * @method static void success(string $message, ?string $key = null, bool $unique = false) Add a success notice.
 * @method static void info(string $message, ?string $key = null, bool $unique = false) Add an info notice.
 * @method static void warning(string $message, ?string $key = null, bool $unique = false) Add a warning notice.
 * @method static void error(string $message, ?string $key = null, bool $unique = false) Add an error notice.
 */
class Notice
{
    /**
     * @var string
     */
    public const ERROR = 'error';

    /**
     * @var string
     */
    public const SUCCESS = 'success';

    /**
     * @var string
     */
    public const WARNING = 'warning';

    /**
     * @var string
     */
    public const INFO = 'info';

    /**
     * Add a notice by calling the method with the status name.
     *
     * @param string $name The status name. Must be one of the constants: ERROR, SUCCESS, WARNING, INFO.
     * @param array  $arguments The arguments to pass to the add() method.
     */
    public static function __callStatic($name, $arguments): void
    {
        if (in_array($name, [self::ERROR, self::SUCCESS, self::WARNING, self::INFO], true)) {
            self::add($name, ...$arguments);
        }
    }

    /**
     * Callback for the admin_notices action.
     * Prints the notices in the admin page.
     */
    public static function admin_notices(): void
    {
        $messages = static::get_lists();
        if ($messages && is_array($messages)) {
            foreach ($messages as $message) {
                echo sprintf(
                    '<div class="notice notice-%s is-dismissible %s">%s</div>',
                    esc_html($message['status']),
                    esc_html(BRICKSBENDER::WP_OPTION),
                    esc_html($message['message'])
                );
            }
        }
    }

    /**
     * Get lists of notices.
     *
     * @param bool $purge If true, the notices will be purged after being retrieved.
     * @return array The list of notices.
     */
    public static function get_lists(?bool $purge = true): array
    {
        $notices = get_option(BRICKSBENDER::WP_OPTION, []);

        if ($purge) {
            update_option(BRICKSBENDER::WP_OPTION, []);
        }

        return $notices;
    }

    /**
     * Add a notice.
     *
     * @param string $status The status of the notice. Must be one of the constants: ERROR, SUCCESS, WARNING, INFO.
     * @param string $message The message to display.
     * @param null|string $key The key to use to identify the notice. If not provided, the notice will be added without a key or sequence number.
     * @param bool $unique If true, the notice will be added only if it is not already in the list of notices.
     */
    public static function add(string $status, string $message, ?string $key = null, bool $unique = false): void
    {
        if (! in_array($status, [self::ERROR, self::SUCCESS, self::WARNING, self::INFO], true)) {
            return;
        }

        $notices = get_option(BRICKSBENDER::WP_OPTION, []);

        $payload = [
            'status' => $status,
            'message' => $message,
        ];

        if ($unique && $key === null && in_array([
            'status' => $status,
            'message' => $message,
        ], $notices, true)) {
            return;
        }

        if ($key) {
            $notices[$key] = $payload;
        } else {
            $notices[] = $payload;
        }

        update_option(BRICKSBENDER::WP_OPTION, $notices);
    }
}
