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

use PrestaShopHeaven\Runtime\Kit\Definition\ModuleDefinitionInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * The hook actionAdminControllerSetMedia for the main class: loads the scripts of ModuleDefinition::ADMIN_SCRIPTS on
 * the module's own pages. Register the hook in ModuleDefinition::HOOKS. A module that needs more (a style, a script
 * on a core page) writes its own hook and calls AdminAssets.
 */
trait AdminScripts
{
    /**
     * Returns the module definition (runtime-kit).
     */
    abstract public function getModuleDefinition(): ModuleDefinitionInterface;

    /**
     * Adds the scripts of the current page (none on other pages).
     *
     * @param array<string, mixed> $params hook parameters (unused)
     */
    public function hookActionAdminControllerSetMedia(array $params): void
    {
        $controller = $this->context->controller;
        AdminAssets::addScripts($this, $controller, AdminAssets::scriptsFor($controller, $this->getModuleDefinition()->adminScripts()));
    }
}
