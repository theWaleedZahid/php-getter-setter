# PHP Getter Setter

A Getter and Setter Library for PHP. Requires PHP>=5.4

You can use methods like `setFoo('bar')` and `getFoo()`. You don't need to create all the methods in the class. This library will do it for you.

It uses `Taits`, built-in PHP and I have used my Annotations Parser Library(`DocBlock`) to specifys Type of property if you want

## Installation

Installation is easy. Just Download the Repository and copy `src` folder in server directory.

## Usage

Just add this in your classes:

```PHP
require 'src/PHPGetSet/GetterSetter.php';
```

Example:

```PHP
<?php
require 'src/PHPGetSet/GetterSetter.php';

Class MyClass{
    use \PHPGetSet\GetterSetter;

    protected $foo;
}

$myClass = new MyClass;

$myClass->setFoo('bar');
echo $myClass->getFoo();
```

**Thats' It.**

### Restrict Getter or Setter or Both

You can use annotation in you class property if you want to disable setter, getter or both using `@setter` and `@getter` annotation variables.
```PHP
/**
 * We can't use setSomeProperty() anymore.
 *
 * @var
 * @setter false
 */
protected $someProperty;
```
___
```PHP
/**
 * We can't use getSomeProperty() anymore.
 *
 * @var \stdClass
 * @getter false
 */
protected $someProperty;
```
___

```PHP
/**
 * We can't use setSomeProperty() or getSomeProperty().
 *
 * @getter false
 * @setter false
 */
protected $someProperty;
```

### Force a Type or Class
You can specify a type for the property using `@var` annotation variable, so setter will take only a value of this type, else it will throw an exception. The code below will work similar as <code>public function setSomeProperty(stdClass $value){}</code>

```PHP
/**
 * Should be an instance of stdClass only.
 *
 * @var \stdClass
 */
protected $shouldBeStdClass;
```
___
```PHP
/**
 * Should be an array only.
 *
 * @var Array
 */
protected $shouldBeArray;
```
___
```PHP
/**
 * Should be a string only
 *
 * @var String
 */
protected $shouldBeString;
```
___
```PHP
/**
 * Should be a number only.
 *
 * @var Number
 */
protected $shouldBeNumber;
```
___
```PHP
/**
 * Should be an object only.
 *
 * @var Object
 */
protected $shouldBeObject;
```

## Notes

PHPGetSet assumes that you use proper camelCase. So name your properties like `$pdoInstance` (not `$PDOInstance`) and call `setPdoInstance()` method.