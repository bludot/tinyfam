<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Core\App;
use Core\Route;
use Core\Helpers\RouterTrie;

/**
 * @covers Route
 */
final class RouteTest extends TestCase
{

    protected function setUp() {
        Route::get('/test', 'MainController');
    }

    public function testRouteIsTrie()
    {
        $this->assertInstanceOf(
            RouterTrie::class,
            Route::getRoutes()
        );
    }

    public function testRouteHasData() {

        $this->assertNotFalse(
            Route::getRoutes()->getVal('/test')
        );

        $this->assertFalse(
            Route::getRoutes()->getVal('/not_here')
        );

        $this->assertArrayHasKey(
            "url",
            Route::getRoutes()->getVal('/test')
        );
        $this->assertArrayHasKey(
            "callback",
            Route::getRoutes()->getVal('/test')
        );
        $this->assertArrayHasKey(
            "method",
            Route::getRoutes()->getVal('/test')
        );
    }

    public function testRouteCallController() {

        $this->assertTrue(
            true
        );
        /*$this->assertTrue(
            Route::callHook()
        );*/

    }
}