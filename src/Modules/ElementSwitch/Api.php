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

namespace Yabe\Bricksbender\Modules\ElementSwitch;

use Bricks\Elements;
use WP_REST_Request;
use WP_REST_Response;
use WP_REST_Server;
use Yabe\Bricksbender\Api\AbstractApi;
use Yabe\Bricksbender\Api\ApiInterface;
use Yabe\Bricksbender\Elements\Loader as ElementsLoader;
use Yabe\Bricksbender\Modules\Loader as ModulesLoader;
use Yabe\Bricksbender\Utils\Config;

/**
 * @since 2.0.0
 */
class Api extends AbstractApi implements ApiInterface
{
    public function __construct()
    {
    }

    public function get_prefix(): string
    {
        return 'modules/m/element-switch';
    }

    public function register_custom_endpoints(): void
    {
        if (!ModulesLoader::get_instance()->get_modules()['element_switch']['instanceWithoutConstructor']->is_enabled()) {
            return;
        }

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
            $this->get_prefix() . '/update-status/(?P<namespace>[^/]+)/(?P<id>[^/]+)',
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
        $elements = [];

        foreach (Elements::$elements as $e) {
            if (strpos($e['class'], 'Yabe\Bricksbender\Elements\\') === 0) {
                continue;
            }

            $eDetails = Elements::get_element(['name' => $e['name']]);

            $elements[] = [
                'namespace' => 'bricks',
                'id' => $e['name'],
                'name' => $eDetails['label'] ?: $e['name'],
                'category' => $eDetails['category'],
                'icon' => $eDetails['icon'],
            ];
        }

        foreach (ElementsLoader::get_instance()->get_elements() as $e) {
            $elements[] = [
                'namespace' => 'bricksbender',
                'id' => $e['name'],
                'name' => $e['label'],
                'category' => $e['instanceWithoutConstructor']->category,
                'icon' => $e['instanceWithoutConstructor']->icon,
            ];
        }

        // loop to add the enabled status
        foreach ($elements as $key => $element) {
            $elements[$key]['status'] = (bool) Config::get(sprintf(
                'elements.%s.%s.enabled',
                $element['namespace'],
                $element['id']
            ), true);
        }

        return new WP_REST_Response([
            'data' => [
                'elements' => $elements,
            ],
        ]);
    }

    private function update_status(WP_REST_Request $wprestRequest): WP_REST_Response
    {
        $url_params = $wprestRequest->get_url_params();
        $payload = $wprestRequest->get_json_params();

        $id = (string) $url_params['id'];
        $ns = (string) $url_params['namespace'];
        $status = (bool) $payload['status'];

        Config::set(sprintf('elements.%s.%s.enabled', $ns, $id), $status);

        do_action('a!yabe/bricksbender/api/module/m/element-switch:update_status', $ns, $id);

        return new WP_REST_Response([
            'id' => $id,
            'status' => $status,
        ], 200, []);
    }
}
