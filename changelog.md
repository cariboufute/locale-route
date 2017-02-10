# Changelog

All Notable changes to `locale-route` will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) 
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Next]
### To add

- Implementing pull requests.

## [1.1.1] – 2017-02-09
### Added

- Laravel 5.1 to 5.4 support in same version.

## [1.1.0] – 2017-02-09
### Added

- Laravel 5.4 support. Laravel 5.1 to 5.3 support kept for version 1.0.

## [1.0.0] – 2016-12-05
### Changed

- Tag for 1.0.0 – First official release.

## [1.0.0-beta6] – 2016-11-27
### Added

- Refactored *LocaleRoute::resource* method.

## [1.0.0-beta5] – 2016-11-22
### Added

- Feature of overriding *locales* and *add_locale_to_url* config options.
- You can add a URL string to have same URL for all locales.

### Changed

- Massive refactoring for more supple LocaleRoute usage, now can be used under Route::group()

### Removed

- Unnecessary *SubRoute* facade and class, replaced with more supple usage of *LocaleRoute*. Please check documentation for new usage.
- Unnecessary *LocaleRoute::group*.
- Temporarily, *LocaleRoute::resource*. Soon will have a DRYer resource method with no locale duplication of non GET routes.

## [1.0.0-beta4] – 2016-11-22
### Added

- *SubRoute* façade to use under *LocaleRoute::group*, so the URLs are still translated with *lang* files.

### Changed

- Given key 'locale.session' to SetSessionLocale middleware, to show shorter name in route:list

## [1.0.0-beta3] – 2016-11-17
### Added

- PHP 5.6 support

### Removed
- Default parameters in *other_route* helper function. Undefined parameters now is always no parameters. *other_locale* keeps default parameters.

## [1.0.0-beta2] – 2016-11-15
### Added

- Adding middleware option

## [1.0.0-beta] – 2016-11-12
### Changed

- Now in beta phase.

## [1.0.0-alpha] - 2016-11-09
### Added
- *LocaleRoute::resource* method

### Changed
- *locale_route*, *other_locale* and *other_route* keep current parameters by default when not declared.

## [1.0.0-alpha] - 2016-11-07
### Added
- *LocaleRoute::group* method
- Functional tests for all *LocaleGroup* methods

