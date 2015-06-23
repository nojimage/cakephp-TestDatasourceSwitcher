<?php

/**
 *
 * Copyright 2015 ELASTIC Consultants Inc.
 *
 */

namespace TestDatasourceSwitcher\Routing\Filter;

use Cake\Event\Event;
use Cake\Network\Request;
use Cake\Routing\DispatcherFilter;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;

/**
 * データソースをtestに切り替える
 *
 * debug >= 1 で稼働
 * Cookieを判別に使用します。デフォルト: __cakephp_test_connection
 *
 * use TestDatasourceSwitcher\Routing\Filter\Switcher;
 * DispatcherFactory::add(new Switcher(['priority' => 1]));
 */
class Switcher extends DispatcherFilter
{

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

    public function beforeDispatch(Event $event)
    {
        // apply debug only
        if (!Configure::read('debug')) {
            return;
        }

        $request = $event->data['request'];
        /* @var $request Request */

        $token = $request->cookie($this->_config['cookieName']);

        if (!is_null($token) && (empty($this->_config['validToken']) || $this->_config['validToken'] == $token)) {
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
