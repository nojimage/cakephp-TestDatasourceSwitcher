# TestDatasourceSwitcher plugin for CakePHP 3.x

Switching datasouce connections to `test`, when the request have a specific Cookie.

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```
composer require nojimage/cakephp-test-datasource-switcher
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

The MIT License

Copyright (c) 2015 ELASTIC Consultants Inc. [https://elasticconsultants.com/](https://elasticconsultants.com/)

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
