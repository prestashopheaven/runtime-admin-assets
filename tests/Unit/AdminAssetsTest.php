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

namespace PrestaShopHeaven\Runtime\AdminAssets\Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use PrestaShopHeaven\Runtime\AdminAssets\AdminAssets;
use PrestaShopHeaven\Runtime\AdminAssets\AdminScripts;
use PrestaShopHeaven\Runtime\Kit\Definition\AbstractModuleDefinition;
use PrestaShopHeaven\Runtime\Kit\Definition\ModuleDefinitionInterface;

/**
 * Scripts and styles only on the module's own pages, with the version against old caches; never on other pages or
 * for something that is not a back-office controller.
 */
final class AdminAssetsTest extends TestCase
{
    private const MAP = ['AdminHeavenfooRules' => ['admin-rules', 'admin-rule-form'], 'AdminHeavenfooLog' => ['admin-log']];

    public function testTheScriptsOfTheCurrentPageAreAddedWithTheVersion(): void
    {
        $controller = $this->controller('AdminHeavenfooRules');

        $added = AdminAssets::addScripts(new \Module(), $controller, AdminAssets::scriptsFor($controller, self::MAP));

        self::assertSame(['/modules/heavenfoo/views/js/admin-rules.js?v=1.2.3', '/modules/heavenfoo/views/js/admin-rule-form.js?v=1.2.3'], $added);
        self::assertSame($added, $controller->js);
    }

    public function testStylesAreAddedTheSameWay(): void
    {
        $controller = $this->controller('AdminHeavenfooLog');

        self::assertSame(['/modules/heavenfoo/views/css/admin.css?v=1.2.3'], AdminAssets::addStyles(new \Module(), $controller, ['admin']));
        self::assertSame(['/modules/heavenfoo/views/css/admin.css?v=1.2.3'], $controller->css);
    }

    /**
     * Controllers that get nothing: another page, no name, an empty name, not an object.
     *
     * @return iterable<string, array{0: mixed}>
     */
    public static function otherControllers(): iterable
    {
        yield 'a core page' => [self::controllerOf('AdminOrders')];
        yield 'no controller_name' => [new \stdClass()];
        yield 'empty controller_name' => [self::controllerOf('')];
        yield 'not a string' => [self::controllerOf(12)];
        yield 'not an object' => [null];
    }

    #[DataProvider('otherControllers')]
    public function testOtherPagesGetNoScripts(mixed $controller): void
    {
        self::assertSame([], AdminAssets::scriptsFor($controller, self::MAP));
    }

    public function testTheControllerName(): void
    {
        self::assertSame('AdminHeavenfooLog', AdminAssets::controllerName($this->controller('AdminHeavenfooLog')));
        self::assertNull(AdminAssets::controllerName('AdminHeavenfooLog'));
    }

    public function testAnyModuleLikeObjectWorksAndMissingPartsAreEmpty(): void
    {
        $standIn = new class {
            /** @var string */
            public $version = '2.0.0';

            /**
             * The module path of the stand-in.
             */
            public function getPathUri(): string
            {
                return '/modules/standin/';
            }
        };

        self::assertSame(['/modules/standin/views/js/a.js?v=2.0.0'], AdminAssets::addScripts($standIn, $this->controller('x'), ['a']));
        self::assertSame(['views/css/b.css?v='], AdminAssets::addStyles(new \stdClass(), $this->controller('x'), ['b']));
    }

    public function testAControllerThatCannotAddAssetsGetsNothing(): void
    {
        self::assertSame([], AdminAssets::addScripts(new \Module(), new \stdClass(), ['admin-log']));
        self::assertSame([], AdminAssets::addStyles(new \Module(), 'AdminHeavenfooLog', ['admin']));
    }

    public function testTheHookTraitLoadsTheScriptsOfTheDefinition(): void
    {
        $module = new class extends \Module {
            use AdminScripts;

            /**
             * A definition with scripts for one page.
             */
            public function getModuleDefinition(): ModuleDefinitionInterface
            {
                return new class extends AbstractModuleDefinition {
                    public const ADMIN_SCRIPTS = ['AdminHeavenfooLog' => ['admin-log']];
                };
            }
        };
        $controller = self::controllerOf('AdminHeavenfooLog');
        $module->context = (object) ['controller' => $controller];

        $module->hookActionAdminControllerSetMedia([]);

        self::assertSame(['/modules/heavenfoo/views/js/admin-log.js?v=1.2.3'], $controller->js);
    }

    /**
     * A back-office controller stand-in that records what is added.
     */
    private function controller(string $name): object
    {
        return self::controllerOf($name);
    }

    /**
     * A back-office controller stand-in with the given controller_name.
     */
    private static function controllerOf(mixed $name): object
    {
        return new class($name) {
            /** @var list<string> */
            public array $js = [];

            /** @var list<string> */
            public array $css = [];

            /**
             * Holds the name.
             */
            public function __construct(public mixed $controller_name)
            {
            }

            /**
             * Records a script.
             */
            public function addJS(string $url): void
            {
                $this->js[] = $url;
            }

            /**
             * Records a style.
             */
            public function addCSS(string $url): void
            {
                $this->css[] = $url;
            }
        };
    }
}
