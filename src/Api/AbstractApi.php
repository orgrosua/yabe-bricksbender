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

namespace Yabe\Bricksbender\Api;

use BRICKSBENDER;
use WP_REST_Request;

class AbstractApi
{
    public const API_NAMESPACE = BRICKSBENDER::REST_NAMESPACE;

    protected function permission_callback(WP_REST_Request $wprestRequest): bool
    {
        return wp_verify_nonce($wprestRequest->get_header('X-WP-Nonce'), 'wp_rest') && current_user_can('manage_options');
    }
}
