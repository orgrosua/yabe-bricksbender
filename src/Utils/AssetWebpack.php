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
 * Manifest friendly assets manager.
 *
 * @todo Add translation support. Use filter hook
 *
 * @since 1.0.0
 */
class AssetWebpack
{
    public static array $manifest = [];

    public static array $entrypoints = [];

    /**
     * Load the asset manifest.
     */
    public static function read_manifest(): array
    {
        if (static::$manifest === []) {
            $manifest = file_get_contents(dirname(BRICKSBENDER::FILE) . '/build/manifest.json');
            $manifest = json_decode($manifest, true, 512, JSON_THROW_ON_ERROR);

            static::$manifest = $manifest;
        }

        return static::$manifest;
    }

    /**
     * Load the asset entrypoints.
     * Entrypoints is generated from webpack.config.js.
     */
    public static function read_entrypoints(): array
    {
        if (static::$entrypoints === []) {
            $entrypoints = file_get_contents(dirname(BRICKSBENDER::FILE) . '/build/entrypoints.json');
            $entrypoints = json_decode($entrypoints, true, 512, JSON_THROW_ON_ERROR);

            static::$entrypoints = $entrypoints['entrypoints'];
        }

        return static::$entrypoints;
    }

    /**
     * Load the asset entrypoints.
     *
     * @param string $key The entrypoint key that defined in entrypoints.json.
     * @param array $deps The dependencies for the manifest. format: `['manifest_key' => ['script_dep_handle', 'style_dep_handle']]`
     * @param bool $in_footer Whether to enqueue the script before </body> instead of in the <head>.
     */
    public static function enqueue_entry(string $key, array $deps = [], bool $in_footer = false): void
    {
        $entrypoints = static::read_entrypoints();

        $manifest = static::read_manifest();

        if (isset($entrypoints[$key])) {
            $entry = $entrypoints[$key];

            if (isset($entry['js'])) {
                foreach ($entry['js'] as $js) {
                    $manifest_key = array_search($js, $manifest, true);

                    $dependency = $deps[$manifest_key] ?? [];

                    /**
                     * Filter the dependency for the manifest key.
                     *
                     * @param array $dependency The dependency.
                     * @param string $deps The manifest key.
                     * @param string $key The entrypoint key.
                     */
                    $dependency = apply_filters('f!yabe/bricksbender/asset:enqueue_entry.dependency', $dependency, $manifest_key, $key);

                    static::enqueue_script($manifest_key, $dependency, '', $in_footer);
                }
            }

            if (isset($entry['css'])) {
                foreach ($entry['css'] as $css) {
                    $manifest_key = array_search($css, $manifest, true);

                    $dependency = $deps[$manifest_key] ?? [];

                    /**
                     * Filter the dependency for the manifest key.
                     *
                     * @param array $dependency The dependency.
                     * @param string $deps The manifest key.
                     * @param string $key The entrypoint key.
                     */
                    $dependency = apply_filters('f!yabe/bricksbender/asset:enqueue_entry.dependency', $dependency, $manifest_key, $key);

                    static::enqueue_style($manifest_key, $dependency);
                }
            }
        }
    }

    /**
     * Register a script.
     *
     * @param string $key The manifest key.
     * @param array $deps The dependencies.
     * @param string $src The source.
     * @param bool $in_footer Whether to enqueue the script before </body> instead of in the <head>.
     * @return bool|string The handle on success, false on failure.
     */
    public static function register_script(string $key, array $deps = [], string $src = '', bool $in_footer = false)
    {
        $handle = BRICKSBENDER::WP_OPTION . ':' . $key;

        if (wp_script_is($handle, 'registered')) {
            return $handle;
        }

        if ($src === '') {
            $manifest = static::read_manifest();

            if (isset($manifest[$key])) {
                $src = strncmp($manifest[$key], 'http', strlen('http')) === 0 ? $manifest[$key] : plugins_url('build/' . $manifest[$key], BRICKSBENDER::FILE);
            } else {
                return false;
            }
        }

        $is_registered = wp_register_script($handle, $src, $deps, BRICKSBENDER::VERSION, $in_footer);

        if ($is_registered) {
            return $handle;
        }

        return false;
    }

    /**
     * Enqueue a script.
     *
     * @param string $key The manifest key.
     * @param array $deps The dependencies.
     * @param string $src The source.
     * @param bool $in_footer Whether to enqueue the script before </body> instead of in the <head>.
     */
    public static function enqueue_script(string $key, array $deps = [], string $src = '', bool $in_footer = false)
    {
        $handle = static::register_script($key, $deps, $src, $in_footer);

        if ($handle !== false) {
            if (wp_script_is($handle, 'enqueued')) {
                return $handle;
            }

            $wp_scripts = wp_scripts();
            $wp_scripts->enqueue($handle);

            return $handle;
        }

        return false;
    }

    /**
     * Register a style.
     *
     * @param string $key The manifest key.
     * @param array $deps The dependencies.
     * @param string $src The source.
     * @param string $media The media for which this stylesheet has been defined.
     * @return bool|string The handle on success, false on failure.
     */
    public static function register_style(string $key, array $deps = [], string $src = '', string $media = 'all')
    {
        $handle = BRICKSBENDER::WP_OPTION . ':' . $key;

        if (wp_style_is($handle, 'registered')) {
            return $handle;
        }

        if ($src === '') {
            $manifest = static::read_manifest();

            if (isset($manifest[$key])) {
                $src = strncmp($manifest[$key], 'http', strlen('http')) === 0 ? $manifest[$key] : plugins_url('build/' . $manifest[$key], BRICKSBENDER::FILE);
            } else {
                return false;
            }
        }

        $is_registered = wp_register_style($handle, $src, $deps, BRICKSBENDER::VERSION, $media);

        if ($is_registered) {
            return $handle;
        }

        return false;
    }

    /**
     * Enqueue a style.
     *
     * @param string $key The manifest key.
     * @param array $deps The dependencies.
     * @param string $src The source.
     * @param string $media The media for which this stylesheet has been defined.
     */
    public static function enqueue_style(string $key, array $deps = [], string $src = '', string $media = 'all')
    {
        $handle = static::register_style($key, $deps, $src, $media);

        if ($handle !== false) {
            if (wp_style_is($handle, 'enqueued')) {
                return $handle;
            }

            $wp_styles = wp_styles();
            $wp_styles->enqueue($handle);

            return $handle;
        }

        return false;
    }

    /**
     * Get the asset base absolute path.
     *
     * @return string The asset base absolute path.
     */
    public static function asset_base_url(): string
    {
        return plugins_url('build/', BRICKSBENDER::FILE);
    }
}
