<?php

namespace App\Tags;

use App\Versioning\VersionManager;
use Statamic\Facades\Entry;
use Statamic\Facades\Site;
use Statamic\Fields\Value;
use Statamic\Tags\Tags;

class NewVersionDetails extends Tags
{
    protected VersionManager $versionManager;

    public function __construct(VersionManager $versionManager)
    {
        $this->versionManager = $versionManager;
    }

    protected function notFound(): array
    {
        return [
            'found_new_version' => false,
        ];
    }

    public function index()
    {
        $slug = $this->context->get('slug');

        if ($slug instanceof Value) {
            $slug = $slug->value();
        }

        $collection = $this->context->get('collection')?->value();
        $mountCollection = $collection?->mount()?->collection();

        if ($mountCollection && $mountCollection?->handle() != $collection?->handle() && $mountCollection?->handle() != 'pages') {
            $collection = $mountCollection;
        }

        if (! $collection) {
            return $this->notFound();
        }

        $version = $this->versionManager->getVersionInformationForCollection($collection);

        if (! $version || ! $version->latestVersion || ! array_key_exists('documentation_collection', $version->latestVersion)) {
            return $this->notFound();
        }

        $defaultSite = Site::default()->handle();
        $currentSite = Site::current()->handle();

        if ($defaultSite != $currentSite) {
            $entry = Entry::whereCollection($version->latestVersion['documentation_collection'])
                ->where('slug', $slug)
                ->where('locale', $currentSite)
                ->first();

            if (! $entry) {
                $entry = Entry::whereCollection($version->latestVersion['documentation_collection'])
                    ->where('slug', $slug)
                    ->first();
            }
        } else {
            $entry = Entry::whereCollection($version->latestVersion['documentation_collection'])
                ->where('slug', $slug)
                ->first();
        }

        $found = $entry !== null;
        $url = $found ? $entry->url() : null;

        return [
            'found_new_version' => $found,
            'new_version_title' => $found ? $entry->title : null,
            'new_version_url' => $url,
        ];
    }
}
