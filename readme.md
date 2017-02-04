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

**This should be the last beta version before "official" release.**

## Change log

Please see [changelog](changelog.md) for more information what has changed recently and what will be added soon.

## Requirements

- PHP 5.6 or later, fully compatible with PHP 7.0
- Laravel 5.1 or later

## Install

First install the package through Composer by typing this line in the terminal at the root of your Laravel application.

``` bash
composer require cariboufute/locale-route 1.0.0-beta6
```

Add the service provider and the ```LocaleRoute``` alias in ```config/app.php```.

``` php
// config/app.php

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
php artisan vendor:publish

#if for some reason, you only want to get locale-route config file, type this line instead
php artisan vendor:publish --provider "CaribouFute\LocaleRoute\LocaleRouteServiceProvider"
```

Then you should have a ```config/localeroute.php``` installed.

## Configuration


Check your ```config/localeroute.php``` file. Here is the default file.
``` php
// config/localeroute.php

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
// routes/web.php or app/Http/routes.php

LocaleRoute::get('route', 'Controller@getAction', ['fr' => 'url_fr', 'en' => 'url_en']);
LocaleRoute::post('route', 'Controller@postAction', ['fr' => 'url_fr', 'en' => 'url_en']);
LocaleRoute::put('route', 'Controller@putAction', ['fr' => 'url_fr', 'en' => 'url_en']);
LocaleRoute::patch('route', 'Controller@patchAction', ['fr' => 'url_fr', 'en' => 'url_en']);
LocaleRoute::delete('route', 'Controller@deleteAction', ['fr' => 'url_fr', 'en' => 'url_en']);
LocaleRoute::options('route', 'Controller@optionsAction', ['fr' => 'url_fr', 'en' => 'url_en']);

// You can also use `any`
LocaleRoute::any('route', 'Controller@getAction', ['fr' => 'url_fr', 'en' => 'url_en']);

```

For the first line, it is the equivalent of declaring this in pure Laravel, while having the app locale set to the right locale.
``` php
Route::get('fr/url_fr', ['as' => 'fr.route', 'uses' => 'Controller@getAction']);
Route::get('en/url_en', ['as' => 'en.route', 'uses' => 'Controller@getAction']);
```

You can also give a string as locale URL if it is the same for all locales

```php
LocaleRoute::get('route', 'Controller@getAction', 'url');

/*
    This will give these routes.

    ['fr.route']    =>  'fr/url'
    ['en.route']    =>  'en/url'
*/
```

So the syntax can be resumed to this.

```
LocaleRoute::{method}({routeName}, {Closure or controller action}, {locale URL string or array with 'locale' => 'url'});
```

#### Using translator files for URLs

You can also use the Laravel translator to put all your locale URLs in ```resources/lang/{locale}/routes.php``` files. If there is no locale URL array, ```LocaleRoute``` will automatically check for the translated ```routes.php``` files to find URLs. All you need to do is to remove the locale URL array in ```LocaleRoute``` and declare them as ```'route' => 'url'``` in your translated route files, like this. 

``` php
// routes/web.php or app/Http/routes.php

LocaleRoute::get('route', 'Controller@routeAction');
```

``` php
// resources/lang/en/routes.php

return [
    'route' => 'url_en',
]
```

``` php
// resources/lang/fr/routes.php

return [
    'route' => 'url_fr',
]
```

#### Note about localized and unlocalized routes using same base URL

If you declare localized and unlocalized routes using the same base URL, *please declare your LocaleRoute method before the Route method*. If you don't, the normal route will be discarded by the locale route attribution process.

For instance, if you declare a normal route with ```"/"``` to redirect to the fallback locale (for instance, ```"/en"```) before the localized routes (for instance, ```"/en"``` and ```"/fr"```), the localized routes with replace the first route before being added the locale. Declaring locale routes before the normal unlocalized route will cause no problems.

```php
/**
 * Here, the '/' URL will be discarded. Only "/fr" and "/en" will exist.
 */

Route::get('/', function () {
    return redirect('/fr');
});

LocaleRoute::get('index', 'PublicController@index', ['fr' => '/', 'en' => '/']);


/**
 * Here, all routes with work fine : "/", "/fr" and "/en".
 */

LocaleRoute::get('index', 'PublicController@index', ['fr' => '/', 'en' => '/']);

Route::get('/', function () {
    return redirect('/fr');
});

```

### Middleware

If you want to use middleware for your LocaleRoute, add them in the url array (3rd parameter) in the ```'middleware'``` key.

``` php
//routes/web.php or app/Http/routes.php

LocaleRoute::get('route', 'Controller@getAction', ['fr' => 'url_fr', 'en' => 'url_en', 'middleware' => 'guest']);

//To use trans files URL, just add 'middleware'
LocaleRoute::get('route', 'Controller@getAction', ['middleware' => 'guest']);

```

### Grouping

You can use the ```LocaleRoute``` methods inside normal ```Route::group``` methods. 

``` php
// routes/web.php or app/Http/routes.php


Route::group(['as' => 'article.', 'prefix' => 'article'], function () {
    LocaleRoute::get('create', 'ArticleController@index', ['fr' => 'creer', 'en' => 'create']);
    Route::post('store', ['as' => 'store', 'uses' => 'ArticleController@store']);
});

/*
Will give these routes :

[fr.article.create] => GET  "/fr/article/creer"     => ArticleController::create()
[en.article.create] => GET  "/en/article/create"    => ArticleController::create()
[article.store]     => POST "/article/store"        => ArticleController::store()
*/
```

### Resource

To add a localized RESTful resource, just use ```LocaleRoute::resource()``` with the same syntax as ```Route::resource```. This will give localized routes for all GET/HEAD routes and will keep the POST/PUT/PATCH/DELETE routes unlocalized.

```php 
// routes/web.php or app/Http/routes.php

LocaleRoute::resource('article', 'ArticleController');

/*
Will give these routes :

[fr.article.index]  => GET/HEAD     "/fr/article"                   => ArticleController::index()
[en.article.index]  => GET/HEAD     "/en/article"                   => ArticleController::index()
[fr.article.show]   => GET/HEAD     "/fr/article/{article}"         => ArticleController::show()
[en.article.show]   => GET/HEAD     "/en/article/{article}"         => ArticleController::show()
[fr.article.create] => GET/HEAD     "/fr/article/create"            => ArticleController::create()
[en.article.create] => GET/HEAD     "/en/article/create"            => ArticleController::create()
[article.store]     => POST         "/article"                      => ArticleController::store()
[fr.article.edit]   => GET/HEAD     "/fr/article/{article}/edit"    => ArticleController::edit()
[en.article.edit]   => GET/HEAD     "/en/article/{article}/edit"    => ArticleController::edit()
[article.update]    => PUT/PATCH    "/article/{article}"            => ArticleController::update()
[article.destroy]   => DELETE       "/article/{article}"            => ArticleController::destroy()
*/
```

If you want to translate the *create* and *edit* words in resources routes URL, add *route-labels.php* lang files in the *resources/lang* folder with translation for *create* and *edit*.

```php
// resources/lang/fr/route-labels.php

return [
    'create' => 'creer',
    'edit' => 'editer',
];
```

```php
// resources/lang/en/route-labels.php

return [
    'create' => 'create',
    'edit' => 'edit',
];
```

```php
// routes/web.php or app/Http/routes.php
LocaleRoute::resource('article', 'ArticleController');

/*
Will give these routes :

[fr.article.create] => GET/HEAD     "/fr/article/creer"             => ArticleController::create()
[en.article.create] => GET/HEAD     "/en/article/create"            => ArticleController::create()
[fr.article.edit]   => GET/HEAD     "/fr/article/{article}/editer"  => ArticleController::edit()
[en.article.edit]   => GET/HEAD     "/en/article/{article}/edit"    => ArticleController::edit()
...
*/
```


### Overriding options

You can override the ```locale``` and ```add_locale_to_url``` config options simply by declaring them in the url array.

``` php
/*
    Config::get('localeroute.locales') => ['fr', 'en']
    Config::get('localeroute.add_locale_to_url') => true
*/

LocaleRoute::get('index', 'Controller@index', ['fr' => '/', 'en' => '/']);

/*
    ['fr.index']    => '/fr'
    ['en.index']    => '/en'
*/

LocaleRoute::get('create', 'Controller@create', [
        'fr' => 'creer',
        'en' => 'create',
        'de' => 'erstellen',
        'locales' => ['fr', 'en', 'de']
]);

/*
    ['fr.create']    => '/fr/creer'
    ['en.create']    => '/en/create'
    ['de.create']    => '/de/erstellen'
*/

LocaleRoute::get('store', 'Controller@store', [
        'fr' => 'stocker',
        'en' => 'store',
        'add_locale_to_url' => false
]);

/*
    ['fr.store']    => '/stocker'
    ['en.store']    => '/store'
*/
```

### Fetching URLs

```LocaleRoute``` gives three helper functions to help you get your URLs quickly. They are close to the Laravel ```route``` helper function.

#### locale_route

This is the basic helper function. It calls the URL according to the locale, route name and parameters. When put to null, locale and route are set to the current values.
``` php
//locale_route($locale, $route, $parameters)

locale_route('fr', 'route');                //gets the French route URL.
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

other_route('route');                   //gets the route URL in the current locale.
other_route('article', ['id' => 1]);    //gets the article route URL in the current locale with parameter 'id' to 1 in the current locale
other_route('article')                  //gets the article route URL in the current locale with no parameters.
```

#### other_locale

Calls the same route URL in another locale. For the syntax, we just replace the route name by the locale. Perfect for language selectors.
``` php
//other_locale($locale, $parameters)

other_locale('es');                     //gets the same URL in Spanish.
other_locale('en', ['id' => 1]);        //gets the same URL in English with parameter 'id' to 1.
other_locale('fr')                      //gets the same URL in French with current parameters.
other_locale('de', [])                  //gets the same URL in German with no parameters, when there are parameters in the current route.
```

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
