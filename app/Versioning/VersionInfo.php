<?php

namespace App\Versioning;

use Statamic\Entries\Collection;
use Statamic\Entries\Entry;

class VersionInfo
{
    public string $documentationCollectionHandle = '';

    public ?Collection $documentationCollection = null;

    public ?Entry $softwareProject = null;

    public string $activeVersionNumber = '';

    public string $activeVersionName = '';

    public string $activeProductName = '';

    public array $availableVersions = [];

    public ?array $latestVersion = null;

    public ?array $activeVersion = null;

    public bool $isMostRecentVersion = false;

    public bool $showVersionWarning = false;

    public function toArray(): array
    {
        if ($this->softwareProject == null) {
            return [];
        }

        return [
            'documentation_collection_handle' => $this->documentationCollectionHandle,
            'active_version_number' => $this->activeVersionNumber,
            'active_product_name' => $this->activeProductName,
            'active_version_name' => $this->activeVersionName,
            'available_versions' => $this->availableVersions,
            'show_version_warning' => $this->showVersionWarning,
            'is_latest_version' => $this->isMostRecentVersion,
            'latest_version' => $this->latestVersion,
            'project' => $this->softwareProject,
            'active_version' => $this->activeVersion,
            'logo' => $this->softwareProject['project_logo'],
        ];
    }
}
