<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Statamic\Facades\Blueprint;
use Statamic\Facades\Collection as CollectionApi;
use Statamic\Facades\Entry as EntryApi;
use Statamic\Facades\YAML;
use Statamic\Structures\CollectionStructure;
use Statamic\Support\Str;

use function Laravel\Prompts\select;

class CreateVersion extends Command
{
    protected $signature = 'tidal:create-version';

    public function handle()
    {
        $projects = EntryApi::whereCollection('software_projects')->all();

        if (count($projects) == 0) {
            $this->info('No projects found.');

            return;
        }

        $options = [];

        foreach ($projects as $project) {
            $options[] = $project->title;
        }

        $option = select('Select a project to add a version to.', $options);

        if (! $option) {
            return;
        }

        $this->info('Gathering information for '.$option.'...');

        $project = $projects[array_search($option, $options)];
        $existingVersions = $project->get('project_versions') ?? [];

        if (count($existingVersions) > 0) {
            $existingDetails = [];

            foreach ($existingVersions as $version) {
                $existingDetails[] = [
                    'name' => $version['version_name'],
                    'number' => $version['version_number'],
                ];
            }

            $this->info($option.' has the following versions:');
            $this->table(['Version Name', 'Number'], $existingDetails);
        } else {
            $this->info('No existing versions found for '.$option.'.');
        }

        $versionName = $this->ask('What is the name of the new version?');
        $versionNumber = $this->ask('What is the version number?');

        if (! $versionName || ! $versionNumber) {
            $this->info('Version name and number are required.');

            return;
        }

        if (count($existingVersions) > 0) {
            foreach ($existingVersions as $version) {
                if ($version['version_name'] == $versionName || $version['version_number'] == $versionNumber) {
                    $this->info('A version with that name or number already exists.');

                    return;
                }
            }
        }

        $documentationCollection = $this->ask('What is the handle of the documentation collection?');

        $collection = CollectionApi::find($documentationCollection);

        if (! $collection) {
            $yesNo = $this->confirm('Collection not found. Would you like to create it?', true);

            if ($yesNo) {
                $collectionTitle = $this->ask('What is the title for the new collection?');
                $routePattern = $this->ask('What would you like the route to be? You should keep it simple, like "project-name/v2/{slug}".');

                if (! $collectionTitle || ! $routePattern) {
                    $this->info('Collection title and route pattern are required.');

                    return;
                }

                $this->info('Creating collection...');

                $collection = CollectionApi::make($documentationCollection)
                    ->title($collectionTitle)
                    ->routes($routePattern)
                    ->layout('layout')
                    ->template('documentation/show')
                    ->defaultPublishState(true)
                    ->dated(true)
                    ->pastDateBehavior('public')
                    ->futureDateBehavior('private')
                    ->previewTargets([
                        [
                            'id' => Str::random(10),
                            'label' => 'Entry',
                            'format' => '{permalink}',
                            'refresh' => true,
                        ],
                    ]);

                $structure = new CollectionStructure();
                $structure->maxDepth(2);
                $structure->expectsRoot(false);
                $structure->showSlugs(false);
                $collection->structure($structure);

                $collection->save();

                $this->info('Collection created.');
                $this->info('Creating blueprints...');
                $pageBuilderContent = file_get_contents(__DIR__.'/stubs/documentation_page.yaml');
                $markdownContent = file_get_contents(__DIR__.'/stubs/documentation.yaml');

                $pageBuilderContent = strtr($pageBuilderContent, [
                    '{handle}' => $documentationCollection,
                ]);

                $markdownContent = strtr($markdownContent, [
                    '{handle}' => $documentationCollection,
                ]);

                $pageBuilder = YAML::parse($pageBuilderContent);
                $markdown = YAML::parse($markdownContent);

                $blueprintNamespace = 'collections.'.$documentationCollection;

                if (! Blueprint::in($blueprintNamespace)->has('documentation_page')) {
                    $pageBuilderBlueprint = Blueprint::make()->setContents($pageBuilder);
                    $pageBuilderBlueprint->setNamespace('collections.'.$documentationCollection);
                    $pageBuilderBlueprint->setHandle('documentation_page');
                    $pageBuilderBlueprint->save();
                }

                if (! Blueprint::in($blueprintNamespace)->has('documentation')) {
                    $markdownBlueprint = Blueprint::make()->setContents($markdown);
                    $markdownBlueprint->setNamespace('collections.'.$documentationCollection);
                    $markdownBlueprint->setHandle('documentation');
                    $markdownBlueprint->save();
                }
            }
        }

        $this->info('Adding version to project...');

        $existingVersions[] = [
            'id' => 'l'.Str::random(7),
            'version_name' => $versionName,
            'version_number' => $versionNumber,
            'documentation_collection' => $documentationCollection,
            'github_settings' => [
                'github_repository' => '',
                'github_edit_root',
            ],
        ];

        $existingVersions = collect($existingVersions)->sortBy('version_number')->toArray();

        $project->set('project_versions', $existingVersions);
        $project->saveQuietly();

        $this->info('Version added to project.');
        $this->info("Don't forget to add the version to your search, sitemap, or social media image config!");
    }
}
