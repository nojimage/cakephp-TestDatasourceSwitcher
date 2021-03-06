<?php
/**
 * Copyright (c) 2019 ELASTIC Consultants Inc. (https://elasticconsultants.com/)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright (c) 2019, ELASTIC Consultants Inc. (https://elasticconsultants.com/)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 *
 */

namespace TestDatasourceSwitcher\Test\TestCase\Middleware;

use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use TestDatasourceSwitcher\Middleware\DatasourceSwitchMiddleware;

/**
 * Test for DatasourceSwitchMiddleware
 */
class DatasourceSwitchMiddlewareTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        if (version_compare(Configure::version(), '3.3.0', '<')) {
            $this->markTestSkipped('This test require CakePHP >= 3.3.0');
        }

        $this->presetConnection();
    }

    public function tearDown()
    {
        ConnectionManager::drop('default');
        ConnectionManager::drop('custom_i18n_datasource');
        ConnectionManager::dropAlias('default');
        ConnectionManager::dropAlias('custom_i18n_datasource');
        parent::tearDown();
    }

    /**
     * add connection for tests
     *
     * @return void
     */
    private function presetConnection()
    {
        ConnectionManager::drop('default');
        ConnectionManager::drop('custom_i18n_datasource');
        ConnectionManager::dropAlias('default');
        ConnectionManager::dropAlias('custom_i18n_datasource');

        $method = method_exists(ConnectionManager::class, 'setConfig')
            ? 'setConfig'
            : 'config';

        ConnectionManager::$method('default', ConnectionManager::parseDsn('sqlite:///:memory:'));
        ConnectionManager::$method('custom_i18n_datasource', ConnectionManager::parseDsn('sqlite:///:memory:'));
    }

    public function testAliasConnections()
    {
        $switcher = new DatasourceSwitchMiddleware();
        $switcher->aliasConnections();

        $this->assertSame('test', ConnectionManager::get('default')->config()['name']);
        $this->assertSame('test_custom_i18n_datasource', ConnectionManager::get('custom_i18n_datasource')->config()['name']);
    }

    public function testInvoke()
    {
        $request = (new ServerRequest())
            ->withCookieParams([
                '__cakephp_test_connection' => '1',
            ]);
        $response = new Response();
        $next = function () {
        };

        $switcher = new DatasourceSwitchMiddleware();
        $switcher($request, $response, $next);

        // switch to test connection
        $this->assertSame('test', ConnectionManager::get('default')->config()['name']);
        $this->assertSame('test_custom_i18n_datasource', ConnectionManager::get('custom_i18n_datasource')->config()['name']);
    }

    public function testInvokeWithOutCookie()
    {
        $request = new ServerRequest();
        $response = new Response();
        $next = function () {
        };

        $switcher = new DatasourceSwitchMiddleware();
        $switcher($request, $response, $next);

        // switch to test connection
        $this->assertSame('default', ConnectionManager::get('default')->config()['name']);
        $this->assertSame('custom_i18n_datasource', ConnectionManager::get('custom_i18n_datasource')->config()['name']);
    }
}
