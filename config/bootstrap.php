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

/**
 * Load filter
 */
use Cake\Core\Configure;
use Cake\Error\Middleware\ErrorHandlerMiddleware;
use Cake\Event\EventManager;
use Cake\Http\MiddlewareQueue;
use Payjp\Event;
use TestDatasourceSwitcher\Middleware\DatasourceSwitchMiddleware;
use TestDatasourceSwitcher\Routing\Filter\Switcher;

if (version_compare(Configure::version(), '3.3', '>=')) {
    // ミドルウェア追加
    EventManager::instance()->on('Server.buildMiddleware', function (Event $event, MiddlewareQueue $queue) {
        $queue->insertAfter(ErrorHandlerMiddleware::class, new DatasourceSwitchMiddleware());
    });
} else {
    \Cake\Routing\DispatcherFactory::add(new Switcher(['priority' => 1]));
}
