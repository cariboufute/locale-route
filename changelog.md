# Changelog

All Notable changes to `locale-route` will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) 
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Next]
### To add

- Implementing localized parameters pull request.

## [3.0.1] - 2021-08-13

- Fixed non converted array actions in route declaration.

## [3.0.0] - 2021-08-12
### Updated

- Changed requirements for security issues.
- Updated packages for security issues.
- Tested Laravel 8 with actual code, no problem detected.

## [2.1.3] - 2020-03-17
### Updated

- Tested Laravel 7 with actual code, no problem detected.

## [2.1.2] – 2019-09-22
### Updated
- Packages, including Symfony packages having security issues. The updated versions fix these issues.

## [2.1.1] – 2019-09-22
### Verified
- Laravel 6.0 support (no code change needed).
- Documentation to inform Laravel 6.0 support.

## [2.1.0] – 2019-08-09
### Added
- Localized version of ```apiResource``` method.

## [2.0.0] – 2019-02-24
### Changed
- Removed old routing class name checking from Laravel pre-5.5, so now the code is cleaner, but Laravel pre-5.5 is lost.

## [1.4.0] – 2018-05-05
### Added
- Laravel 5.6 support.
- Support of trailing methods after LocaleRoute call (ex.: `LocaleRoute::get([...])->where([...]);`)

## [1.3.0] – 2017-09-01
### Added
- Laravel 5.5 Package Discovery parameters in composer.json for automatic adding of service provider and ```LocaleRoute``` façade.

## [1.2.1] – 2017-04-02
### Changed

- Helpers functions now fall back to default route handling when no locale route is available. This keeps *other_locale* helper function from raising errors in unlocalized pages.

## [1.2.0] – 2017-04-01
### Added

- LocaleRoute::any method.

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

