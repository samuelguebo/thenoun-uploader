
# The Noun Uploader 

![PHP Composer](https://github.com/samuelguebo/thenoun-uploader/workflows/PHP%20Composer/badge.svg) ![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)

Drag and drop uploader for icons from The Noun project. It provides a convenient and quick process for importing into Commons icons that were previously downloaded from [thenounproject.com](https://thenounproject.com/)
![](https://thenoun-uploader.toolforge.org/medias/thenoun-uploader-screenshot.png)

## Requirements

* [PHP 7.3+](https://www.php.net/downloads.php)
* [Composer (PHP dependency manager)](https://getcomposer.org/download/)

## Installing dependencies
Let composer find and install latest versions of dependencies
```bash 
composer update
```

## Configuration
Rename `settings-sample.php` to `settings.php` and change the parameters according to your Oauth consumer app. 

Checkout [MediaWiki  documentation ](https://www.mediawiki.org/wiki/Help:OAuth) for more details on creating your Oauth credentials.

## Quickstart the app
```bash
php -S localhost:5000
```

## Unit testing
Do some testing by running the following command 
```bash
composer test
```

## Todo
- More test coverage
- Consider [wikimedia/toolforge-bundle](https://packagist.org/packages/wikimedia/toolforge-bundle) for better structure and long-term maintenance

## Contributors 
 * Samuel Guebo ([@samuelguebo](https://twitter.com/samuelguebo))
