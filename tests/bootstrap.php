<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/MIT
 *
 * @author    prestahaven.com
 * @copyright Since 2026 George van Engers
 * @license   https://opensource.org/licenses/MIT MIT License
 */

/*
 * Unit tests of runtime-admin-assets run without PrestaShop: the _PS_VERSION_ guard and a Module stand-in with the
 * path and version AdminAssets uses (only when the real class is not loaded).
 */

if (!defined('_PS_VERSION_')) {
    define('_PS_VERSION_', '0.0.0-unit');
}

if (!class_exists('Module', false)) {
    /**
     * Module with a path URI, a version and a context.
     */
    class Module
    {
        /** @var string */
        public $version = '1.2.3';

        /** @var string */
        public $name = 'heavenfoo';

        /** @var object|null */
        public $context;

        /**
         * The module's URL path.
         */
        public function getPathUri(): string
        {
            return '/modules/' . $this->name . '/';
        }
    }
}
