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

use Bricks\Elements as BricksElements;
use Exception;
use ReflectionClass;
use BRICKSBENDER;
use Symfony\Component\Finder\Finder;
use Yabe\Bricksbender\Utils\Config;

class Loader
{
    /**
     * List of Elements services.
     *
     * @var ElementInterface[]
     */
    private array $elements = [];

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
        if (!isset(self::$instance)) {
            self::$instance = new self();
            self::$instance->scan_elements();
        }

        return self::$instance;
    }

    public function scan_elements()
    {
        // Get cached Elements
        $transient_name = 'bricksbender_scanned_elements_' . BRICKSBENDER::VERSION;

        /** @var ElementInterface[]|false $cached */
        $cached = get_transient($transient_name);

        if (!WP_DEBUG && $cached !== false) {
            $this->elements = $cached;

            return;
        }

        $finder = new Finder();
        $finder->files()->in(__DIR__)->name('*.php');

        /**
         * Add additional places to scan for Elements.
         *
         * @param Finder $finder The Finder instance.
         */
        do_action('a!yabe/bricksbender/element/loader:scan_elements.before_scan', $finder);

		// Load abstract element base class
		require_once BRICKS_PATH . 'includes/elements/base.php';
		require_once BRICKS_PATH . 'includes/elements/container.php';

        foreach ($finder as $file) {
            $element_file = $file->getPathname();

            if (!is_readable($element_file)) {
                continue;
            }

            require_once $element_file;
        }

        // Find any Elements that extends ElementInterface class
        $declared_classes = get_declared_classes();

        foreach ($declared_classes as $declared_class) {
            if (!class_exists($declared_class)) {
                continue;
            }

            $reflector = new ReflectionClass($declared_class);

            if (!$reflector->isSubclassOf(ElementInterface::class)) {
                continue;
            }

            // Get Element detail and push to Loader::$elements to be register later
            /** @var ElementInterface $element */
            $element = $reflector->newInstanceWithoutConstructor();

            $this->elements[$element->get_identifier()] = [
                'name' => $element->get_identifier(),
                'file_path' => $reflector->getFileName(),
                'class_name' => $reflector->getName(),
                'label' => $element->get_label(),
                'instanceWithoutConstructor' => $element,
            ];
        }

        // Cache the scanned Elements
        set_transient($transient_name, $this->elements, HOUR_IN_SECONDS);
    }

    /**
     * Register Elements.
     */
    public function register_elements(): void
    {
        $elements = apply_filters('f!yabe/bricksbender/element/loader:register_elements', $this->elements);

        foreach ($elements as $element) {
            // Check if the Element is enabled
            if (!Config::get(sprintf('elements.bricksbender.%s.enabled',  $element['name']), true)) {
                continue;
            }

            BricksElements::register_element(
                $element['file_path'],
                $element['name'],
                $element['class_name'],
            );
        }
    }

    /**
     * Get the list of Elements.
     *
     * @return ElementInterface[]
     */
    public function get_elements(): array
    {
        return $this->elements;
    }
}
