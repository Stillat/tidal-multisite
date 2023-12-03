<?php

namespace App\Tags;

use App\Versioning\VersionManager;
use Statamic\Facades\Collection as CollectionApi;
use Statamic\Facades\Entry as EntryApi;
use Statamic\Tags\Tags;

class GetProjectUrl extends Tags
{
    protected VersionManager $versionManager;

    public function __construct(VersionManager $versionsManager)
    {
        $this->versionManager = $versionsManager;
    }

    protected function notFound()
    {
        return [
            'found_url' => false,
        ];
    }

    public function index()
    {
        $project = EntryApi::find($this->params->get('project'));

        if (! $project) {
            return $this->notFound();
        }

        $versions = $project->get('project_versions');

        if (! $versions || count($versions) == 0) {
            return $this->notFound();
        }

        $lastVersion = $versions[count($versions) - 1];

        if (! array_key_exists('documentation_collection', $lastVersion)) {
            return $this->notFound();
        }

        $firstEntry = CollectionApi::find($lastVersion['documentation_collection'])
            ->queryEntries()
            ->orderBy('order')
            ->where('is_section', false)
            ->first();

        if (! $firstEntry) {
            return $this->notFound();
        }

        return [
            'found_url' => true,
            'url' => $firstEntry->url(),
        ];
    }
}
