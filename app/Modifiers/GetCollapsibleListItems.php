<?php

namespace App\Modifiers;

use Statamic\Modifiers\Modifier;

class GetCollapsibleListItems extends Modifier
{
    public function index($value, $params, $context)
    {
        $parts = [];

        foreach ($value as $item) {
            $parts[] = '{ id: \''.$item['id'].'\', is_open: '.(($item['open_by_default'] ?? false) ? 'true' : 'false').' }';
        }

        return '['.implode(', ', $parts).']';
    }
}
