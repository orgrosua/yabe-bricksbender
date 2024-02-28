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

namespace Yabe\Bricksbender\Module;

use Exception;
use ReflectionClass;
use Symfony\Component\Finder\Finder;

class Loader
{
    /**
     * List of Modules services.
     *
     * @var ModuleInterface[]
     */
    private array $modules = [];

    /**
     * Stores the instance, implementing a Singleton pattern.
     */
    private static self $instance;

    /**
     * The Singleton's constructor should always be private to prevent direct
     * construction calls with the `new` operator.
     */
    private function __construct()
    {
    }

    /**
     * Singletons should not be cloneable.
     */
    private function __clone()
    {
    }

    /**
     * Singletons should not be restorable from strings.
     *
     * @throws Exception Cannot unserialize a singleton.
     */
    public function __wakeup()
    {
        throw new Exception('Cannot unserialize a singleton.');
    }

    /**
     * This is the static method that controls the access to the singleton
     * instance. On the first run, it creates a singleton object and places it
     * into the static property. On subsequent runs, it returns the client existing
     * object stored in the static property.
     */
    public static function get_instance(): self
    {
        if (! isset(self::$instance)) {
            self::$instance = new self();
            self::$instance->scan_modules();
        }
        return self::$instance;
    }

    public function scan_modules()
    {
        $finder = new Finder();
        $finder->files()->in(__DIR__)->name('*.php');

        /**
         * Add additional places to scan for Modules integration.
         *
         * @param Finder $finder The Finder instance.
         */
        do_action('a!yabe/bricksbender/module/integration:before_scan', $finder);

        foreach ($finder as $file) {
            $module_file = $file->getPathname();

            if (! is_readable($module_file)) {
                continue;
            }

            require_once $module_file;
        }

        // Find any Modules integration that extends ModuleInterface class
        $declared_classes = get_declared_classes();

        foreach ($declared_classes as $declared_class) {
            if (! class_exists($declared_class)) {
                continue;
            }

            $reflector = new ReflectionClass($declared_class);

            if (! $reflector->isSubclassOf(ModuleInterface::class)) {
                continue;
            }

            // Get Modules integration detail and push to Integration::$modules to be register later
            /** @var ModuleInterface $module */
            $module = $reflector->newInstanceWithoutConstructor();

            $this->modules[$module->get_name()] = [
                'name' => $module->get_name(),
                'file_path' => $reflector->getFileName(),
                'class_name' => $reflector->getName(),
            ];
        }

        return $this;
    }

    /**
     * Register Modules.
     */
    public function register_modules(): void
    {
        /**
         * Filter the Modules before register.
         *
         * @param ModuleInterface[] $modules
         * @return ModuleInterface[]
         */
        /** @var ModuleInterface[] $modules */
        $modules = apply_filters('f!yabe/bricksbender/module/integration:register_modules', $this->modules);

        foreach ($modules as $module) {
            // Create new instance of Module class and register custom endpoints
            /** @var ModuleInterface $moduleInstance */
            $moduleInstance = new $module['class_name']();
            $this->modules[$module['name']]['instance'] = $moduleInstance;
        }
    }

    /**
     * Get the list of Modules.
     * 
     * @return ModuleInterface[]
     */
    public function get_modules(): array
    {
        return $this->modules;
    }
}
