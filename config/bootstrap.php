<?php

use Cake\Routing\DispatcherFactory;
use SwitchTestDatasource\Routing\Filter\Switcher;
DispatcherFactory::add(new Switcher(['priority' => 1]));
