<?php

namespace App\Tags;

use Illuminate\Support\Str;
use Statamic\Facades\Data;
use Statamic\Tags\Tags;

class CurrentFilePath extends Tags
{
    public function index()
    {
        $id = $this->context->get('id');
        $data = Data::find($id);

        if (! $data) {
            return [];
        }

        $dataPath = str_replace('\\', '/', $data->path());
        $basePath = str_replace('\\', '/', base_path('/'));

        $dataPath = str_replace('//', '/', $dataPath);
        $basePath = str_replace('//', '/', $basePath);

        return [
            'current_path' => Str::after($dataPath, $basePath),
        ];
    }
}
