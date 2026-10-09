<?php

/*
 * Bootstraps a new Sylius project from the skeleton.
 *
 * This file only lives in the skeleton: it is removed, along with the other
 * skeleton-only files, once the application is installed.
 */

namespace sylius_starter\init;

use Castor\Attribute\AsRawTokens;
use Castor\Attribute\AsTask;

use function Castor\context;
use function Castor\fs;
use function Castor\input;
use function Castor\io;
use function Castor\run;
use function qa\find_sylius_service;

/**
 * @param list<string> $options the options of "castor docker:service:install sylius" (--with-name, --with-theme, …)
 */
#[AsTask(name: 'init', namespace: 'sylius', description: 'Create a new Sylius application from this skeleton', ignoreValidationErrors: true)]
function init(#[AsRawTokens] array $options = []): void
{
    io()->title('Sylius Starter');

    if (null !== find_sylius_service()) {
        io()->error('A Sylius application is already registered in castor.php.');

        return;
    }

    $command = ['castor', 'docker:service:install', 'sylius', ...$options];
    $context = context();

    if (input()->isInteractive()) {
        $context = $context->toInteractive();
    } elseif (!\in_array('-n', $options, true) && !\in_array('--no-interaction', $options, true)) {
        $command[] = '--no-interaction';
    }

    run($command, context: $context);

    // A new process, so the services castor.php now registers are loaded.
    run(['castor', 'sylius:init:finalize']);
}

#[AsTask(name: 'finalize', namespace: 'sylius:init', description: 'Turn the skeleton into your project once Sylius is installed (run by sylius:init)')]
function finalize(): void
{
    $sylius = find_sylius_service();

    if (null === $sylius) {
        io()->error('No Sylius application is registered in castor.php: run "castor sylius:init" first.');

        return;
    }

    $root = \dirname(__DIR__, 2);
    $fs = fs();

    io()->section('Cleaning up the skeleton');

    // Sylius-Standard ships its own Docker stack, Makefile and CI: Castor replaces them.
    $fs->remove(array_map(
        static fn (string $file): string => $sylius->getDirectory() . '/' . $file,
        ['compose.yml', 'compose.override.dist.yml', 'Makefile', '.github'],
    ));

    // The CI and README of the skeleton make way for the ones of the project.
    if (is_dir($root . '/.github.dist')) {
        $fs->remove($root . '/.github');
        $fs->rename($root . '/.github.dist', $root . '/.github');
    }

    if (is_file($root . '/README.dist.md')) {
        $fs->rename($root . '/README.dist.md', $root . '/README.md', true);
    }

    // The project files refer to the application as "app" in "app/": use the name and directory it was given.
    $directory = $fs->makePathRelative($sylius->getDirectory(), $root);
    $replacements = [
        'castor app:' => \sprintf('castor %s:', $sylius->getName()),
        '`app/`' => \sprintf('`%s`', $directory),
        '(app/)' => \sprintf('(%s)', $directory),
    ];

    foreach ([$root . '/README.md', $root . '/AGENTS.md', $root . '/.github/workflows/ci.yml'] as $file) {
        if (is_file($file)) {
            file_put_contents($file, strtr((string) file_get_contents($file), $replacements));
        }
    }

    $fs->remove([$root . '/LICENSE', __FILE__]);

    io()->success('Your Sylius project is ready!');

    io()->listing([
        '<info>castor docker:about</info>: the URLs of your project (admin: <comment>/admin</comment>, login <comment>sylius</comment> / <comment>sylius</comment>)',
        '<info>castor sylius:add</info>, <info>castor sylius:theme:setup</info>, <info>castor sylius:payment-gateways:setup</info>…: shape your store',
        '<info>castor qa</info>: run the quality checks',
        '<info>castor list</info>: every available task',
    ]);

    io()->note('Commit everything (git add . && git commit -m "Initial commit"), compose.yaml and castor.composer.lock included.');
}
