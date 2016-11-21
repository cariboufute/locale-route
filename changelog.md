# Changelog

All Notable changes to `locale-route` will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) 
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Coming soon]
### To add
- Complete missing unit and functional tests for complete code coverage.

## [dev-master] – 2016-11-20
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

