# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## Unreleased
### Fixed
* Notice for undefined var, #14

## 1.1.0 (2019-02-19)
### Added
* Add option to use post excerpts in post content
* Add composer.json
* Add `category` tag in xml to add each site/blog title to each item

### Improve
* Complete rewrite of entire codebase using PHP 5.6 features
* Improve escaping when rendering feeds
* Update `lastBuildDate` when post in the network change.
* Allow passing query parameters into feed urls.
* Make query args filterable

### Fixed
* Changed ambiguous wording between the "use more" feature and the post excerpt feature
* Fixed notice on saving options

## 1.0.3 (2015-04-14)
### Added
* Add settings options to filter for authors

### Fixed
* Remove fix value for filter 'pre_option_rss_use_excerpt' to set always full or excerpt
Code Maintenance

## 1.0.2 (02/01/2014)
### Added
* Add option for full feed

### Fixed
* Fix on DB select for old installations, before Multiiste (WPMU)

## 1.0.1 (06/20/2013)
### Added
* Add more possibilities on Settings

### Fixed
* Fix small major problems

## 1.0.0
* Initial Release
