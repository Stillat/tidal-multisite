<?php

namespace App\Listeners;

use Exception;
use Illuminate\Support\Facades\Log;
use Statamic\Events\GlobalSetSaved;
use Stillat\StatamicSiteEssentials\Support\Facades\Favicons;
use Stillat\StatamicSiteEssentials\Support\Facades\WebManifest;

class FaviconListener
{
    public function handle(GlobalSetSaved $event): void
    {
        if (! config('site_essentials.favicons.auto_generate')) {
            return;
        }

        if (! $event->globals) {
            return;
        }

        if (! $event->globals->handle() == 'site_settings') {
            return;
        }

        // Prevent missing Imagick from crashing the site.
        if (! class_exists('Imagick')) {
            Log::warning('Favicon generation requires the Imagick PHP extension to be installed.');

            return;
        }

        try {
            Favicons::generate();
            WebManifest::generate();
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
