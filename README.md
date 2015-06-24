# TestDatasourceSwitcher plugin for CakePHP 3.x

Switching datasouce connections to `test`, when the request have a specific Cookie.

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```
composer require --dev nojimage/cakephp-test-datasource-switcher
```

in `config/bootstrap.php`

```
if (Configure::read('debug')) {
    Plugin::load('TestDatasourceSwitcher', ['bootstrap' => true]);
}
```

## Usage

eg. For the Codeception functional testcases.  
 (This sample is always connect to 'test' datasouces.

```
class FunctionalHelper extends \Codeception\Module
{

    public function _before(TestCase $test)
    {
        $driver = $this->getModule('WebDriver');
        /* @var $dirver WebDriver */
        $driver->amOnPage('/');
        $driver->setCookie('__cakephp_test_connection', '1');
    }

(...snip...) 
```

## License

This software is released under the MIT License.

Copyright (c) 2015 ELASTIC Consultants Inc. [https://elasticconsultants.com/](https://elasticconsultants.com/)

http://opensource.org/licenses/mit-license.php
