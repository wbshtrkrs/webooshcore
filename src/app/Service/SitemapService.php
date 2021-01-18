<?php

namespace weboosh\webooshcore\app\Service;

use Spatie\Sitemap\SitemapGenerator;

class SitemapService
{
    public static function generate() {
        $path = env('SITEMAP_FILENAME', null);
        if (class_exists('Spatie\Sitemap\SitemapGenerator') && $path) {
            $path = public_path($path);
            if (!file_exists($path)) {
                \File::put($path, null);
            }

            try {
                SitemapGenerator::create(env('APP_URL', 'https://www.weboosh.id/'))
                    ->writeToFile($path);
            } catch (\Exception $e) {
                \Log::info($e->getMessage());
            }
        }
    }
}
