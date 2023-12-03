<?php

namespace App\Tags;

use Statamic\Exceptions\NotFoundHttpException;
use Statamic\Facades\Site;
use Statamic\Structures\TreeBuilder;
use Statamic\Tags\Tags;

class AutoRedirect extends Tags
{
    public function index()
    {
        $collection = (string) $this->context['collection'];
        $url = (string) $this->context['url'];

        // Locate the first child under the current section.
        $tree = (new TreeBuilder)->build([
            'structure' => 'collection::'.$collection,
            'include_home' => false,
            'from' => $url,
            'site' => Site::current()->handle(),
        ]);

        if (! $tree || count($tree) == 0) {
            throw new NotFoundHttpException();
        }

        $url = $tree[0]['page']->url();

        return "<meta http-equiv=\"refresh\" content=\"0;URL='".$url."'\" />";
    }
}
