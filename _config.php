<?php

use Innoweb\ImageTemplatedShortcode\ImageShortcodeHandler;
use SilverStripe\View\Parsers\ShortcodeParser;

ShortcodeParser::get('default')
    ->register('image', [ImageShortcodeHandler::class, 'handle_shortcode']);
