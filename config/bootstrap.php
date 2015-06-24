<?php

/**
 * Copyright (c) 2015 ELASTIC Consultants Inc. (https://elasticconsultants.com/)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright (c) 2015, ELASTIC Consultatnts Inc. (https://elasticconsultants.com/)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * Load filter
 */
use Cake\Routing\DispatcherFactory;
use TestDatasourceSwitcher\Routing\Filter\Switcher;

DispatcherFactory::add(new Switcher(['priority' => 1]));
