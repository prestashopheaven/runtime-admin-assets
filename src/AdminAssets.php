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

declare(strict_types=1);

namespace PrestaShopHeaven\Runtime\AdminAssets;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Adds a module's scripts and styles to its own back-office pages (HK-05, HK-11; was a copy in each module:
 * src/Module/Hook/ActionAdminControllerSetMedia.php). Only on the pages listed for the current controller, after the
 * admin theme's main.bundle.js (so window.prestashop.component exists), with ?v=<module version> against old caches.
 */
final class AdminAssets
{
    /**
     * The legacy controller name of the current back-office controller, or null when it has none (front office, CLI).
     */
    public static function controllerName(mixed $controller): ?string
    {
        if (!is_object($controller) || !property_exists($controller, 'controller_name')) {
            return null;
        }
        $name = $controller->controller_name;

        return is_string($name) && $name !== '' ? $name : null;
    }

    /**
     * The scripts for the current controller from a map controller name => script names.
     *
     * @param array<string, list<string>> $scriptsByController
     *
     * @return list<string>
     */
    public static function scriptsFor(mixed $controller, array $scriptsByController): array
    {
        $name = self::controllerName($controller);

        return $name === null ? [] : $scriptsByController[$name] ?? [];
    }

    /**
     * Adds views/js/<script>.js of the module for each script; returns the URLs added (none when the controller
     * cannot add scripts).
     *
     * @param object $module the module (getPathUri() and $version; a \Module, or a stand-in in tests)
     * @param list<string> $scripts script names without .js
     *
     * @return list<string>
     */
    public static function addScripts(object $module, mixed $controller, array $scripts): array
    {
        if (!is_object($controller) || !method_exists($controller, 'addJS')) {
            return [];
        }
        $added = [];
        foreach ($scripts as $script) {
            $url = self::baseUri($module) . 'views/js/' . $script . '.js?v=' . self::version($module);
            $controller->addJS($url);
            $added[] = $url;
        }

        return $added;
    }

    /**
     * Adds views/css/<style>.css of the module for each style; returns the URLs added.
     *
     * @param object $module the module (getPathUri() and $version; a \Module, or a stand-in in tests)
     * @param list<string> $styles style names without .css
     *
     * @return list<string>
     */
    public static function addStyles(object $module, mixed $controller, array $styles): array
    {
        if (!is_object($controller) || !method_exists($controller, 'addCSS')) {
            return [];
        }
        $added = [];
        foreach ($styles as $style) {
            $url = self::baseUri($module) . 'views/css/' . $style . '.css?v=' . self::version($module);
            $controller->addCSS($url);
            $added[] = $url;
        }

        return $added;
    }

    /**
     * The URL path of the module folder ('' when the object has no getPathUri()).
     */
    private static function baseUri(object $module): string
    {
        return method_exists($module, 'getPathUri') ? (string) $module->getPathUri() : '';
    }

    /**
     * The version of the module, against old browser caches ('' when the object has none).
     */
    private static function version(object $module): string
    {
        return property_exists($module, 'version') ? (string) $module->version : '';
    }
}
