<?php

namespace App\Tags;

use App\Versioning\VersionManager;
use Statamic\Tags\Concerns\OutputsItems;
use Statamic\Tags\Tags;

class GetVersions extends Tags
{
    use OutputsItems;

    protected VersionManager $versionManager;

    public function __construct(VersionManager $manager)
    {
        $this->versionManager = $manager;
    }

    public function index()
    {
        // Get the collection.
        $collection = $this->context['collection']?->value();

        return $this->output($this->versionManager->getVersionInformationForCollection($collection) ?? []);
    }
}
