# LocaleRoute, for localized testable routes in Laravel

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](license.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

**LocaleRoute** is a package to make testable localized routes with Laravel 5. It comes from the need to have localized routes that are fully testable.

LocaleRoute has a syntax close to the original Laravel routing methods, so the installation and learning curve should be quite easy.

For an example of LocaleRoute implementation, please check my [locale-route-example repo](https://github.com/cariboufute/locale-route-example).

**This package is now on alpha stage. It should be functional. Please check it but test it throughoutly before using it in production.**

## Requirements

- PHP 7
- Laravel 5.3 (should be compatible with 5.1 and 5.2 but has not been tested with these versions yet)

## Install

First install the package through Composer by typing this line in the terminal at the root of your Laravel application.

``` bash
composer require cariboufute/locale-route 1.0.0-alpha
```

Add the service provider and the ```LocaleRoute``` alias in ```config/app.php```.

``` php
'providers' => [
    //...
    CaribouFute\LocaleRoute\LocaleRouteServiceProvider::class,
    //...
],

'aliases' => [
    //...
    'LocaleRoute' => CaribouFute\LocaleRoute\Facades\LocaleRoute::class,
],
```

In your ```app/Http/Kernel.app``` file, add the ```SetLocale``` middleware in the web middleware group. This will read the locale from the ```locale``` session variable, saved by each localized route and will keep the locale for redirections, even after using unlocalized routes to access models CRUD routes, for instance.

``` php
// app/Http/Kernel.app

protected $middlewareGroups = [
    'web' => [
        //...
        \CaribouFute\LocaleRoute\Middleware\SetLocale::class,
    ],

    //...
];
```

Finally install the config file of the package by typing this line in the terminal at the root of your Laravel application.

``` bash
artisan vendor:publish
```

Then you should have a ```config/localeroute.php``` installed.

## Configuration


Check your ```config/localeroute.php``` file. Here is the default file.
``` php
<?php

return [

    /**
     * The locales used by routes. Add all
     * locales needed for your routes.
     */
    'locales' => ['fr', 'en'],

    /**
     * Option to add '{locale}/' before given URIs.
     * For LocaleRoute::get('route', ...):
     * true     => '/fr/route'
     * false    => '/route'
     * Default is true.
     */
    'add_locale_to_url' => true,
];

```

### Locales

Add all the locale codes needed for your website in ```locales```.

For instance, if you want English, French, Spanish and German in your site...
``` php
    'locales' => ['en', 'fr', 'es', 'de'],
```

### Add locale automatically to URLs

This option is by default set to true. It prepends all URLs build by locale-route with a ```{locale}/```, according to [Google Multi-regional and multilingual sites guidelines](https://support.google.com/webmasters/answer/182192?hl=en).

If for any reason, you don't want this prefix to be added automatically, just put this option to false, like this.
``` php
    'add_locale_to_url' => false,
```

## Usage

### Adding routes

Adding localized routes is now really easy. Just go to your ```routes/web.php``` file (or ```app/Http/routes.php``` in older versions of Laravel) and add ```LocaleRoute``` declarations almost like you would declare Laravel ```Route``` methods.

``` php
LocaleRoute::get('route', 'Controller@getAction', ['fr' => 'url_fr', 'en' => 'url_en']);
LocaleRoute::post('route', 'Controller@postAction', ['fr' => 'url_fr', 'en' => 'url_en']);
LocaleRoute::put('route', 'Controller@putAction', ['fr' => 'url_fr', 'en' => 'url_en']);
LocaleRoute::patch('route', 'Controller@patchAction', ['fr' => 'url_fr', 'en' => 'url_en']);
LocaleRoute::delete('route', 'Controller@deleteAction', ['fr' => 'url_fr', 'en' => 'url_en']);
LocaleRoute::options('route', 'Controller@optionsAction', ['fr' => 'url_fr', 'en' => 'url_en']);
```

For the first line, it is the equivalent of declaring this in pure Laravel, while having the app locale set to the right locale.
``` php
Route::get('fr/url_fr', ['as' => 'fr.route', 'uses' => 'Controller@getAction']);
Route::get('en/url_en', ['as' => 'en.route', 'uses' => 'Controller@getAction']);
```

So the syntax can be resumed to this.

```
LocaleRoute::{method}({routeName}, {Closure or controller action}, {locale URL array with 'locale' => 'url'});
```

#### Using translator files for URLs

You can also use the Laravel translator to put all your locale URLs in ```resources/lang/{locale}/routes.php``` files. If there is no locale URL array, ```LocaleRoute``` will automatically check for the translated ```routes.php``` files to find URLs. All you need to do is to remove the locale URL array in ```LocaleRoute``` and declare them as ```'route' => 'url'``` in your translated route files, like this. 

``` php
//routes/web.php

LocaleRoute::get('route', 'Controller@routeAction');
```

``` php
//resources/lang/en/routes.php

return [
    'route' => 'url_en',
]
```

``` php
//resources/lang/fr/routes.php

return [
    'route' => 'url_fr',
]
```

### Fetching URLs

```LocaleRoute``` gives three helper functions to help you get your URLs quickly. They are close to the Laravel ```route``` helper function.

#### locale_route

This is the basic helper function. It calls the URL according to the locale, route name and parameters. When put to null, locale and route are set to the current values.
``` php
//locale_route($locale, $route, $parameters)

locale_route('fr', 'route');            //gets the French route URL.
locale_route('es', 'article', ['id' => 1]); //gets the Spanish article route URL with parameter 'id' set to 1
locale_route(null, 'index');                //gets the index route URL in the current locale
locale_route('en');                         //gets the current URL in English
locale_route('en', null, ['id' => 1]);      //gets the current URL in English, with parameter 'id' set to 1
```

For the last three situations, there are clearer helper functions.

#### other_route

Calls another route URL in the same locale. The syntax is the same as Laravel ```route```.
``` php
//other_route($route, $parameters)

other_route('route');            //gets the route URL in the current locale.
other_route('article', ['id' => 1]); //gets the article route URL with parameter 'id' to 1 in the current locale
```

#### other_locale

Calls the same route URL in another locale. For the syntax, we just replace the route name by the locale. Perfect for language selectors.
``` php
//other_locale($locale, $parameters)

other_locale('es');            //gets the same URL in Spanish.
other_locale('en', ['id' => 1]); //gets the same URL in English with parameter 'id' to 1.
```

## Change log

Please see [changelog](changelog.md) for more information what has changed recently.

## Contributing

Please see [contributing](contributing.md) and [conduct](conduct.md) for details.

## Credits

- [Frédéric Chiasson][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/cariboufute/locale-route.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/cariboufute/locale-route/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/cariboufute/locale-route.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/cariboufute/locale-route.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/cariboufute/locale-route.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/cariboufute/locale-route
[link-travis]: https://travis-ci.org/cariboufute/locale-route
[link-scrutinizer]: https://scrutinizer-ci.com/g/cariboufute/locale-route/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/cariboufute/locale-route
[link-downloads]: https://packagist.org/packages/cariboufute/locale-route
[link-author]: https://github.com/cariboufute
[link-contributors]: ../../contributors
