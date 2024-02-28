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

namespace Yabe\Bricksbender\Admin;

use BRICKSBENDER;
use Yabe\Bricksbender\Utils\AssetVite;

class AdminPage
{
    public function __construct()
    {
        add_action('admin_menu', fn () => $this->add_admin_menu(), 1_000_001);
    }

    public static function get_page_url(): string
    {
        return add_query_arg([
            'page' => BRICKSBENDER::WP_OPTION,
        ], admin_url('admin.php'));
    }

    public function add_admin_menu()
    {
        $hook = add_submenu_page(
            'bricks',
            __('Yabe Bricksbender', 'yabe-bricksbender'),
            __('Yabe Bricksbender', 'yabe-bricksbender'),
            'manage_options',
            BRICKSBENDER::WP_OPTION,
            fn () => $this->render(),
            1_000_001
        );

        add_action('load-' . $hook, fn () => $this->init_hooks());
    }

    private function render()
    {
        add_filter('admin_footer_text', static fn ($text) => 'Thank you for using <b>Bricksbender</b>! Join us on the <a href="https://www.facebook.com/groups/1142662969627943" target="_blank">Facebook Group</a>.', 1_000_001);
        add_filter('update_footer', static fn ($text) => $text . ' | Bricksbender ' . BRICKSBENDER::VERSION, 1_000_001);
        echo '<div id="bricksbender-app" class=""></div>';
    }

    private function init_hooks()
    {
        add_action('admin_enqueue_scripts', fn () => $this->enqueue_scripts(), 1_000_001);
    }

    private function enqueue_scripts()
    {
        AssetVite::get_instance()->enqueue_asset('assets/admin/main.js', [
            'handle' => BRICKSBENDER::WP_OPTION . ':admin',
            'in_footer' => true,
        ]);

        // wp_set_script_translations(BRICKSBENDER::WP_OPTION . ':admin', 'yabe-bricksbender');

        // wp_localize_script(BRICKSBENDER::WP_OPTION . ':admin', 'bricksbender', [
        //     '_version' => BRICKSBENDER::VERSION,
        //     '_wpnonce' => wp_create_nonce(BRICKSBENDER::WP_OPTION),
        //     'web_history' => self::get_page_url(),
        //     'rest_api' => [
        //         'nonce' => wp_create_nonce('wp_rest'),
        //         'root' => esc_url_raw(rest_url()),
        //         'namespace' => BRICKSBENDER::REST_NAMESPACE,
        //         'url' => esc_url_raw(rest_url(BRICKSBENDER::REST_NAMESPACE)),
        //     ],
        //     'assets' => [
        //         'url' => AssetVite::asset_base_url(),
        //     ],
        //     'site_meta' => [
        //         'name' => get_bloginfo('name'),
        //         'site_url' => get_site_url(),
        //     ],
        // ]);
    }
}
