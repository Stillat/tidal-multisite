<?php

namespace App\Modifiers;

use Statamic\Modifiers\Modifier;
use Statamic\Support\Str;

class PublicUrl extends Modifier
{
    public function index($value, $params, $context)
    {
        if (Str::startsWith($value, ['http://', 'https://'])) {
            return $value;
        }

        if (Str::startsWith($value, '/')) {
            $value = Str::after($value, '/');
        }

        $root = Str::finish(config('app.url'), '/');

        return $root.$value;
    }
}
