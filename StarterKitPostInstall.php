<?php

use Laravel\Prompts\Prompt;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\spin;

/*
 * This file contains the post-install hooks for the Tidal Starter Kit.
 *
 * The following methods are from Peak, and are used with permission:
 *
 * - applyInteractivity
 * - installNodeDependencies
 * - run
 * - withSpinner
 * - withoutSpinner
 *
 * Check out Peak at https://github.com/studio1902/statamic-peak
 *
 * Thanks!
 */

class StarterKitPostInstall
{
    protected bool $interactive = true;

    public function handle($console)
    {
        $this->applyInteractivity($console);
        $this->installNodeDependencies();
    }

    protected function applyInteractivity($console): void
    {
        $this->interactive = ! $console->option('no-interaction');
        Prompt::interactive($this->interactive);
    }

    protected function installNodeDependencies(): void
    {
        if (! confirm(label: 'Do you want to install npm dependencies?', default: true)) {
            return;
        }

        $this->run(
            command: 'npm i',
            processingMessage: 'Installing npm dependencies...',
            successMessage: 'npm dependencies installed.',
        );
    }

    protected function run(string $command, string $processingMessage = '', string $successMessage = '', string $errorMessage = null, bool $tty = false, bool $spinner = true, int $timeout = 120): bool
    {
        $process = new Process(explode(' ', $command));
        $process->setTimeout($timeout);

        if ($tty) {
            $process->setTty(true);
        }

        try {
            $spinner ?
                $this->withSpinner(
                    fn () => $process->mustRun(),
                    $processingMessage,
                    $successMessage
                ) :
                $this->withoutSpinner(
                    fn () => $process->mustRun(),
                    $successMessage
                );

            return true;
        } catch (ProcessFailedException $exception) {
            error($errorMessage ?? $exception->getMessage());

            return false;
        }
    }

    protected function withSpinner(callable $callback, string $processingMessage = '', string $successMessage = ''): void
    {
        spin($callback, $processingMessage);

        if ($successMessage) {
            info("[✓] $successMessage");
        }
    }

    protected function withoutSpinner(callable $callback, string $successMessage = ''): void
    {
        $callback();

        if ($successMessage) {
            info("[✓] $successMessage");
        }
    }
}
