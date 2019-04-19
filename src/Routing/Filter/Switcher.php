<?php

/**
 * Copyright (c) 2015 ELASTIC Consultants Inc. (https://elasticconsultants.com/)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright (c) 2015, ELASTIC Consultants Inc. (https://elasticconsultants.com/)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace TestDatasourceSwitcher\Routing\Filter;

use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Cake\Network\Request;
use Cake\Routing\DispatcherFilter;

/**
 * データソースをtestに切り替える
 *
 * debug >= 1 で稼働
 * Cookieを判別に使用します。デフォルト: __cakephp_test_connection
 *
 *     use TestDatasourceSwitcher\Routing\Filter\Switcher;
 *     DispatcherFactory::add(new Switcher(['priority' => 1]));
 */
class Switcher extends DispatcherFilter
{
    /**
     * Switcher constructor.
     *
     * @param array|string $config ['cookieName' => ..., 'validToken' => ...]
     */
    public function __construct($config = [])
    {
        $defaults = [
            'validToken' => null,
            'cookieName' => '__cakephp_test_connection',
        ];

        if (is_string($config)) {
            $config = ['validToken' => $config];
        }

        parent::__construct(array_merge($defaults, $config));
    }

    /**
     * @param Event $event the Event
     * @return void
     */
    public function beforeDispatch(Event $event)
    {
        // apply debug only
        if (!Configure::read('debug')) {
            return;
        }

        $request = $event->data['request'];
        /* @var $request Request */

        $token = $request->cookie($this->_config['cookieName']);

        if ($token !== null && (empty($this->_config['validToken']) || $this->_config['validToken'] === $token)) {
            $this->_aliasConnections();
        }
    }

    /**
     * Add aliases for all non test prefixed connections.
     *
     * This allows models to use the test connections without
     * a pile of configuration work.
     *
     * @return void
     * @see \Cake\TestSuite\Fixture\FixtureManager::_aliasConnections()
     */
    protected function _aliasConnections()
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
