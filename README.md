# SkeltchGo
SkeltchGo is a standalone version of [Glowie](https://github.com/glowieframework/glowie) Skeltch templating engine for PHP, intented to use from outside the framework.

## Requirements
- PHP version 7.4 or higher
- Composer version 2.0 or higher

## Installation
```
composer require glowieframework/skeltchgo
```

## Usage
Create an SkeltchGo instance through the static `make()` method.

```php
use Glowie\SkeltchGo\SkeltchGo;
$skeltch = SkeltchGo::make();
```

This method returns an instance of `ViewRenderer`.

The `make()` method accepts three optional arguments:

- `viewFolder` (string) - Folder where the view files are stored, relative to the running script. (Defaults to `views`)
- `cache` (bool) - Enable views caching. Highly recommended in a production environment. (Defaults to `true`)
- `cacheFolder` (string) - View cache folder, relative to the running script. **Must have writing permissions.** (Defaults to `cache`)

## Rendering views
Views must be `.phtml` files inside the views folder. Extension is not needed.

_From the script_
```php
$skeltch->renderView('myView');
```

_From another view_
```php
{@view('myView')}
```

## Rendering layouts
Layouts must be `.phtml` files inside the views folder. Extension is not needed. The second parameter is an optional view file to render within the layout.

_From the script_
```php
$skeltch->renderLayout('myLayout', 'myView');
```

_From another view_
```php
{@layout('myLayout', 'myView')}
```

To retrieve the internal view content inside the layout use:

```php
{@content}
```

## Passing parameters
There are two ways of passing parameters to the views:

```php
// Globally to all views
$skeltch->view->param1 = 'Lorem ipsum';

// Restricted to a single view
$skeltch->renderView('myView', [
    'param2' => 'Hello world'
]);
```

Then retrieve it in the view as a property of itself:

```php
{{$this->param1}}
```

## View helpers
Setup a helper method by passing a name and a closure to the `helper()` method:

```php
$skeltch->helper('sayHello', function($name){
    return "Hello, $name!";
});
```

And call it in your view file using:

```php
{{$this->sayHello('World')}}
```

## Full documentation
To learn how to use all methods and templating syntax, read [Skeltch complete documentation](https://glowie.tk/docs/latest/extra/skeltch).

> **Note:** some Skeltch methods are restricted to the framework environment and are not available in SkeltchGo. Unavailable methods are: `babel`, `url`, `route`, `asset` and `csrf`.

## Credits
SkeltchGo and Glowie are currently being developed by [Gabriel Silva](https://eugabrielsilva.tk).