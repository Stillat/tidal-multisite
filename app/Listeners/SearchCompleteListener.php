<?php

namespace App\Listeners;

use Statamic\Facades\Site;
use Stillat\DocumentationSearch\Events\SearchComplete;
use Stillat\StatamicSearchReport\Logger;

class SearchCompleteListener
{
    public function handle(SearchComplete $event): void
    {
        Logger::log(
            $event->results->index,
            $event->results->searchTerm,
            Site::current()->handle(),
            $event->results->page,
            $event->results->resultCount
        );
    }
}
