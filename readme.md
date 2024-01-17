# Silverstripe Templated Image Shortcodes

## Overview

Renders image shortcodes (from the WYSIWYG editor) using the default SS image template `DBFile_image`.

This is useful if you want to use for example [resonsive images](https://github.com/heyday/silverstripe-responsive-images) 
or similar for your WYSIWYG images.

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

## Usage

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

If you use [resonsive images](https://github.com/heyday/silverstripe-responsive-images) or other image modules that 
use different templates, I suggest using a custom template for the short codes, so that the original `DBFile_image`
stays unchaned for displaying images in the CMS. (e.g if your responsive image or lazy load functionality requires 
Javascript, that script might not be available in the CMS.)

## License

BSD 3-Clause License, see [License](license.md)
