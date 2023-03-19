# Silverstripe Templated Image Shortcodes

## Overview

Render shortcode images using SS template

## Requirements

* Silverstripe Framework 5
* Silverstripe Assets 2

Note: this version is compatible with Silverstripe 5. 
For Silverstripe 4, please see the [1 release line](https://github.com/innowebau/silverstripe-image-templated-shortcode/tree/1).

## Installation

Install the module using composer:
```
composer require innoweb/silverstripe-image-templated-shortcode dev-master
```
Then run dev/build.

## Configuration

By default the module uses the SS `DBFile_image` template for the images. You can create your own in your theme or 
include the one from this module in your theme stack:

```
SilverStripe\View\SSViewer:
  themes:
    - '$public'
    - 'app'
    - 'yourtheme'
    - 'innoweb/silverstripe-image-templated-shortcode:/templates'
    - '$default'
```

You can also chage the template that is used:

```
Innoweb\ImageTemplatedShortcode\ImageShortcodeHandler:
  template: 'Your_Template'
```

## License

Proprietary
