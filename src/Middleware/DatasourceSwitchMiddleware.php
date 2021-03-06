<?php
/**
 * Copyright (c) 2017 ELASTIC Consultants Inc. (https://elasticconsultants.com/)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright (c) 2017, ELASTIC Consultants Inc. (https://elasticconsultants.com/)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace TestDatasourceSwitcher\Middleware;

use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\TestSuite\Fixture\FixtureManager;

/**
 * データソースをtestに切り替える
 *
 * debug >= 1 で稼働
 * Cookieを判別に使用します。デフォルト: __cakephp_test_connection
 */
class DatasourceSwitchMiddleware
{
    /**
     * @var array
     */
    private $_defaultConfig = [
        'validToken' => null,
        'cookieName' => '__cakephp_test_connection',
    ];

    /**
     * @var array
     */
    private $_config = [];

    /**
     * @param array|string $config ['cookieName' => ..., 'validToken' => ...]
     */
    public function __construct($config = [])
    {
        if (is_string($config)) {
            $config = ['validToken' => $config];
        }

        $this->_config = array_merge($this->_defaultConfig, $config);
    }

    /**
     * @param ServerRequest $request the Request
     * @param Response $response the Response
     * @param callback $next next callback
     * @return Response
     */
    public function __invoke($request, $response, $next)
    {
        // apply debug only
        if (Configure::read('debug')) {
            $token = $request->getCookie($this->_config['cookieName']);

            if ($token !== null && (empty($this->_config['validToken']) || $this->_config['validToken'] === $token)) {
                $this->aliasConnections();
            }
        }

        return $next($request, $response);
    }

    /**
     * Add aliases for all non test prefixed connections.
     *
     * This allows models to use the test connections without
     * a pile of configuration work.
     *
     * @return void
     * @see FixtureManager::_aliasConnections()
     */
    public function aliasConnections()
    {
        $connections = ConnectionManager::configured();
        ConnectionManager::alias('test', 'default');
        $map = [];

        foreach ($connections as $connection) {
            if (in_array($connection, ['default', 'test', 'debug_kit'])) {
                continue;
            }
            if (isset($map[$connection])) {
                continue;
            }
            if (strpos($connection, 'test_') === 0) {
                $map[$connection] = substr($connection, 5);
            } else {
                $map['test_' . $connection] = $connection;
            }
        }

        foreach ($map as $alias => $connection) {
            ConnectionManager::alias($alias, $connection);
        }
    }
}
