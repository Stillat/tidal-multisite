<?php

namespace App\Versioning;

use Statamic\Entries\Collection;
use Statamic\Entries\Entry;
use Statamic\Facades\Collection as CollectionApi;
use Statamic\Facades\Entry as EntryApi;
use Statamic\Facades\Site;

class VersionManager
{
    public function getVersionInformationForCollection(?Collection $collection): ?VersionInfo
    {
        $collectionHandle = $collection?->handle();

        if (! $collectionHandle) {
            return null;
        }

        $softwareProjects = collect(EntryApi::whereCollection('software_projects')->all());
        $activeVersion = null;

        $project = $softwareProjects->firstWhere(function (Entry $entry) use ($collectionHandle, &$activeVersion) {
            $versions = $entry->get('project_versions');

            if (! $versions) {
                return false;
            }

            foreach ($versions as $version) {
                if (! array_key_exists('documentation_collection', $version)) {
                    continue;
                }

                if ($version['documentation_collection'] == $collectionHandle) {
                    $activeVersion = $version;

                    return true;
                }
            }

            return false;
        });

        if (! $project) {
            return null;
        }

        $allVersions = $project->get('project_versions');
        $versions = [];

        $defaultSite = Site::default()->handle();
        $currentSite = Site::current()->handle();

        foreach ($allVersions as $version) {
            if ($defaultSite != $currentSite) {
                $firstEntry = CollectionApi::find($version['documentation_collection'])
                    ->queryEntries()
                    ->orderBy('order')
                    ->where('is_section', false)
                    ->where('site', $currentSite)
                    ->first();

                if (! $firstEntry) {
                    $firstEntry = CollectionApi::find($version['documentation_collection'])
                        ->queryEntries()
                        ->orderBy('order')
                        ->where('is_section', false)
                        ->where('site', $currentSite)
                        ->first();
                }
            } else {
                $firstEntry = CollectionApi::find($version['documentation_collection'])
                    ->queryEntries()
                    ->orderBy('order')
                    ->where('is_section', false)
                    ->first();
            }

            $version['url'] = $firstEntry->absoluteUrl();
            $versions[] = $version;
        }

        $latestVersion = null;
        $isMostRecentVersion = false;

        if (count($allVersions) > 0) {
            $latestVersion = $versions[count($versions) - 1];
            $isMostRecentVersion = $latestVersion['version_number'] == $activeVersion['version_number'];
        }

        $versionInfo = new VersionInfo();
        $versionInfo->documentationCollection = $collection;
        $versionInfo->documentationCollectionHandle = $collection->handle();
        $versionInfo->softwareProject = $project;
        $versionInfo->activeProductName = $project['title'];
        $versionInfo->showVersionWarning = $project['show_version_warning'] ?? false;
        $versionInfo->activeVersionNumber = $activeVersion['version_number'];
        $versionInfo->activeVersionName = $activeVersion['version_name'];
        $versionInfo->activeVersion = $activeVersion;
        $versionInfo->availableVersions = $versions;
        $versionInfo->latestVersion = $latestVersion;
        $versionInfo->isMostRecentVersion = $isMostRecentVersion;

        return $versionInfo;
    }
}
