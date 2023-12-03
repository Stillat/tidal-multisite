<?php

namespace App\Providers;

use App\Versioning\VersionManager;
use Illuminate\Support\ServiceProvider;
use League\CommonMark\Extension\HeadingPermalink\HeadingPermalinkExtension as MarkdownHeadingPermalinkExtension;
use Statamic\Facades\Cascade as CascadeFacade;
use Statamic\Facades\Collection;
use Statamic\Facades\Config;
use Statamic\Facades\GlobalSet;
use Statamic\Facades\Markdown;
use Statamic\View\Cascade;
use Stillat\StatamicBardHeadingPermalinks\HeadingPermalinkExtension;
use Stillat\StatamicSiteEssentials\Support\Facades\Favicons;
use Stillat\StatamicSiteEssentials\Support\Facades\Metadata;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Favicons::getSourceUsing(function () {
            $settings = GlobalSet::find('site_settings')->inDefaultSite();

            if (! $settings) {
                return null;
            }

            $favicon = $settings['favicon'];

            if (! $favicon) {
                return null;
            }

            $assetContainer = $favicon->container();

            return $assetContainer->disk()->path($favicon->path());
        });

        Metadata::resolve('title', function (array $context) {
            if (! array_key_exists('title', $context) && ! array_key_exists('site_settings', $context)) {
                return config('app.name');
            }

            $title = $context['title'] ?? null;
            $siteName = (string) $context['site_settings']['site_name'];

            $isHome = $context['is_homepage'] ?? false;

            if (! $title || $isHome) {
                return $siteName;
            }

            return "$title | $siteName";
        });

        Metadata::resolve('site_name', function (array $context) {
            return (string) $context['site_settings']['site_name'];
        });

        Metadata::withDefaults()->addProviders([
            \Stillat\SocialMediaImageKit\Metadata\MetadataProvider::class,
            \Stillat\StatamicSiteEssentials\Metadata\EssentialMetadataProvider::class,
        ]);

        $this->bootPermalinkExtension();
        $this->bootCascadeData();
        $this->bootFeedamicComputedValues();
    }

    private function bootFeedamicComputedValues(): void
    {
        $feedProfile = config('social_media_image_kit.images.feed_profile', '');

        foreach (config('social_media_image_kit.general.collections') as $collection) {
            Collection::computed($collection, 'asset_feed_image', function ($entry) use ($feedProfile) {
                $feedImage = collect($entry['social_media_images'])->first(function ($image) use ($feedProfile) {
                    return (string) $image['social_media_image_type'] === $feedProfile;
                });

                return $feedImage['asset_social_media_image'] ?? null;
            });
        }

        // Automatically set the image width and height for Feedamic, based on the configured profile.
        $matchingProfile = collect(config('social_media_image_kit.images.profiles', []))->first(function ($profile) use ($feedProfile) {
            return $profile['handle'] === $feedProfile;
        });

        if (! $matchingProfile) {
            return;
        }

        Config::set('feedamic.image.width', $matchingProfile['width']);
        Config::set('feedamic.image.height', $matchingProfile['height']);
    }

    private function bootCascadeData(): void
    {
        CascadeFacade::hydrated(function (Cascade $cascade) {
            /** @var VersionManager $versionManager */
            $versionManager = app(VersionManager::class);
            $collection = $cascade->get('collection')?->value();
            $mountCollection = $collection?->mount()?->collection();

            if ($mountCollection && $mountCollection?->handle() != $collection?->handle() && $mountCollection?->handle() != 'pages') {
                $collection = $mountCollection;
            }

            $versionInfo = $versionManager->getVersionInformationForCollection($collection);

            if ($versionInfo == null) {
                return;
            }

            view()->composer('*', function ($view) use ($versionInfo) {
                $view->with('software_version', $versionInfo->toArray());
            });
        });
    }

    private function bootPermalinkExtension(): void
    {
        HeadingPermalinkExtension::registerAll();

        Markdown::addExtension(function () {
            return new MarkdownHeadingPermalinkExtension();
        });
    }
}
