<?php

namespace App\Modifiers;

use Statamic\Facades\Site;
use Statamic\Fields\Value;
use Statamic\Modifiers\Modifier;

class HideLanguagePicker extends Modifier
{
    /**
     * Modify a value.
     *
     * @param  mixed  $value    The value to be modified
     * @param  array  $params   Any parameters used in the modifier
     * @param  array  $context  Contextual values
     * @return mixed
     */
    public function index($value, $params, $context)
    {
        $currentSite = Site::current()?->handle();

        if (! $currentSite) {
            return true;
        }

        if (count($value) == 1) {
            $first = $value[0];

            if (! array_key_exists('locale', $first)) {
                return true;
            }

            $firstLocale = $first['locale'];

            if (! array_key_exists('handle', $firstLocale)) {
                return true;
            }

            $firstLocale = $firstLocale['handle'];

            if ($firstLocale instanceof Value) {
                $firstLocale = $firstLocale->value();
            }

            if (! is_string($firstLocale)) {
                return true;
            }

            return $firstLocale == $currentSite;
        }

        return false;
    }
}
