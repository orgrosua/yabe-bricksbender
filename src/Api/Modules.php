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

use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use Yabe\Bricksbender\Modules\Loader as ModulesLoader;
use Yabe\Bricksbender\Modules\ModuleInterface;
use Yabe\Bricksbender\Utils\Config;

/**
 * @since 2.0.0
 */
class Modules extends AbstractApi implements ApiInterface
{
    public function __construct()
    {
    }

    public function get_prefix(): string
    {
        return 'modules';
    }

    public function register_custom_endpoints(): void
    {
        register_rest_route(
            self::API_NAMESPACE,
            $this->get_prefix() . '/index',
            [
                'methods' => WP_REST_Server::READABLE,
                'callback' => fn (\WP_REST_Request $wprestRequest): \WP_REST_Response => $this->index($wprestRequest),
                'permission_callback' => fn (\WP_REST_Request $wprestRequest): bool => $this->permission_callback($wprestRequest),
            ]
        );

        register_rest_route(
            self::API_NAMESPACE,
            $this->get_prefix() . '/update-status/(?P<id>[^/]+)',
            [
                'methods' => WP_REST_Server::EDITABLE,
                'callback' => fn (\WP_REST_Request $wprestRequest): \WP_REST_Response => $this->update_status($wprestRequest),
                'permission_callback' => fn (\WP_REST_Request $wprestRequest): bool => $this->permission_callback($wprestRequest),
                'args' => [
                    'status' => [
                        'required' => true,
                        'validate_callback' => static fn ($param): bool => is_bool($param),
                    ],
                ],
            ]
        );
    }

    public function index(WP_REST_Request $wprestRequest): WP_REST_Response
    {
        $modules = ModulesLoader::get_instance()->get_modules();

        $items = [];

        foreach ($modules as $module) {
            /** @var ModuleInterface $moduleInstance */
            $moduleInstance = $module['instanceWithoutConstructor'];
            $items[] = [
                'id' => $module['name'],
                'title' => $moduleInstance->get_title(),
                'description' => $moduleInstance->get_description(),
                'icon' => $moduleInstance->get_icon(),
                'status' => $moduleInstance->is_enabled(),
                'version' => $moduleInstance->get_version(),
                'hasSettingPage' => $moduleInstance->has_setting_page(),
            ];
        }

        return new WP_REST_Response([
            'data' => [
                'modules' => $items,
            ],
        ]);
    }

    private function update_status(WP_REST_Request $wprestRequest): WP_REST_Response
    {
        /** @var wpdb $wpdb */
        global $wpdb;

        $url_params = $wprestRequest->get_url_params();
        $payload = $wprestRequest->get_json_params();

        $id = (string) $url_params['id'];
        $status = (bool) $payload['status'];

        $modules = ModulesLoader::get_instance()->get_modules();

        $module = null;

        // Find the module by id (name)
        foreach ($modules as $moduleItem) {
            if ($moduleItem['name'] === $id) {
                $module = $moduleItem;
                break;
            }
        }

        if ($module === null) {
            return new WP_REST_Response([
                'message' => __('Module not found', 'yabe-bricksbender'),
            ], 404, []);
        }

        /** @var ModuleInterface $moduleInstance */
        $moduleInstance = $module['instanceWithoutConstructor'];

        Config::set(sprintf('modules.%s.enabled', $moduleInstance->get_name()), $status);

        do_action('a!yabe/bricksbender/api/modules:update_status', $module);

        return new WP_REST_Response([
            'id' => $id,
            'status' => $status,
        ]);
    }
}
