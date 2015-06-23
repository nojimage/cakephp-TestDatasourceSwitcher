<?php

use Cake\Routing\DispatcherFactory;
use TestDatasourceSwitcher\Routing\Filter\Switcher;
DispatcherFactory::add(new Switcher(['priority' => 1]));
