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

namespace PrestaShopHeaven\Runtime\AdminAssets\Tests\PhpStan;

use PrestaShopHeaven\Runtime\AdminAssets\AdminScripts;
use PrestaShopHeaven\Runtime\Kit\Definition\AbstractModuleDefinition;
use PrestaShopHeaven\Runtime\Kit\Definition\ModuleDefinitionInterface;

/**
 * Example module that uses AdminScripts, so PHPStan analyses the trait in a real Module subclass. Not used at runtime.
 */
final class ExampleModule extends \Module
{
    use AdminScripts;

    /**
     * Returns an empty definition.
     */
    public function getModuleDefinition(): ModuleDefinitionInterface
    {
        return new class extends AbstractModuleDefinition {
        };
    }
}
