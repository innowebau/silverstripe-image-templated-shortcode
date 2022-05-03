<?php

namespace Innoweb\ImageTemplatedShortcode;

use SilverStripe\Assets\File;
use SilverStripe\Assets\Image;
use SilverStripe\Assets\Shortcodes\ImageShortcodeProvider;
use SilverStripe\Assets\Storage\AssetStore;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\View\Parsers\ShortcodeParser;

class ImageShortcodeHandler extends ImageShortcodeProvider
{

    private static $template = 'DBFile_image';

    /**
     * Replace"[image id=n]" shortcode with an image reference.
     * Permission checks will be enforced by the file routing itself.
     *
     * @param array $args Arguments passed to the parser
     * @param string $content Raw shortcode
     * @param ShortcodeParser $parser Parser
     * @param string $shortcode Name of shortcode used to register this handler
     * @param array $extra Extra arguments
     * @return string Result of the handled shortcode
     */
    public static function handle_shortcode($args, $content, $parser, $shortcode, $extra = []): ?string
    {
        $allowSessionGrant = static::config()->allow_session_grant;

        $cache = static::getCache();
        $cacheKey = static::getCacheKey($args);

        $item = $cache->get($cacheKey);
        if ($item) {
            // Initiate a protected asset grant if necessary
            if (!empty($item['filename']) && $allowSessionGrant) {
                Injector::inst()->get(AssetStore::class)->grant($item['filename'], $item['hash']);
            }

            return $item['markup'];
        }

        // Find appropriate record, with fallback for error handlers
        $fileFound = true;
        $record = static::find_shortcode_record($args, $errorCode);
        if ($errorCode) {
            $fileFound = false;
            $record = static::find_error_record($errorCode);
        }
        if (!$record) {
            return null; // There were no suitable matches at all.
        }

        // Check if a resize is required
        $image = $record;
        $width = null;
        $height = null;
        if ($record instanceof Image) {
            $width = isset($args['width']) ? (int) $args['width'] : null;
            $height = isset($args['height']) ? (int) $args['height'] : null;
            $hasCustomDimensions = ($width && $height);
            if ($hasCustomDimensions && (($width != $record->getWidth()) || ($height != $record->getHeight()))) {
                $resized = $record->ResizedImage($width, $height);
                // Make sure that the resized image actually returns an image
                if ($resized) {
                    $image = $resized;
                }
            }
        }

        // Determine whether loading="lazy" is set
        $args = self::updateLoadingValue($args, $width, $height);

        if (isset($args['class'])) {
            $image = $image->setAttribute('class', $args['class']);
        }
        if (isset($args['loading'])) {
            $image = $image->setAttribute('loading', $args['loading']);
        }

        $template = self::getFrontendTemplate();
        $markup = $image->renderWith($template, [
            'Class' => $args['class'] ?? null,
            'Loading' => $args['loading'] ?? null,
        ])->RAW();

        // cache it for future reference
        if ($fileFound) {
            $cache->set($cacheKey, [
                'markup' => $markup,
                'filename' => $record instanceof File ? $record->getFilename() : null,
                'hash' => $record instanceof File ? $record->getHash() : null,
            ]);
        }

        return $markup;
    }

    private static function getFrontendTemplate()
    {
        if (self::config()->template) {
            return self::config()->template;
        }

        return 'DBFile_image';
    }

    private static function updateLoadingValue(array $args, ?int $width, ?int $height): array
    {
        if (!Image::getLazyLoadingEnabled()) {
            return $args;
        }
        if (isset($args['loading']) && $args['loading'] == 'eager') {
            // per image override - unset the loading attribute unset to eager load (default browser behaviour)
            unset($args['loading']);
        } elseif ($width && $height) {
            // width and height must be present to prevent content shifting
            $args['loading'] = 'lazy';
        }
        return $args;
    }
}
