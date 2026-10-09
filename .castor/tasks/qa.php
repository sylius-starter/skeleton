<?php

/*
 * Quality checks of the Sylius application, run with the tools Sylius-Standard
 * ships (ECS, PHPStan, PHPUnit, Behat) inside its builder container.
 */

namespace qa;

use Castor\Attribute\AsOption;
use Castor\Attribute\AsRawTokens;
use Castor\Attribute\AsTask;
use SyliusStarter\Core\Service\SyliusService;

use function Castor\Docker\collect_services;
use function Castor\Docker\docker_exit_code;
use function Castor\io;

#[AsTask(name: 'all', namespace: 'qa', description: 'Run every quality check', aliases: ['qa'])]
function all(): int
{
    $exitCode = 0;

    foreach ([audit(...), lint(...), cs(...), phpstan(...), phpunit(...)] as $check) {
        $exitCode = max($exitCode, $check());
    }

    return $exitCode;
}

#[AsTask(name: 'audit', namespace: 'qa', description: 'Check the dependencies for security advisories')]
function audit(): int
{
    io()->section('Security audit');

    return sylius_exec(['composer', 'audit', '--abandoned=report']);
}

#[AsTask(name: 'lint', namespace: 'qa', description: 'Lint the container, the configuration, the templates and the Doctrine mapping')]
function lint(): int
{
    io()->section('Linters');

    return max(
        sylius_exec(['php', 'bin/console', 'lint:container']),
        sylius_exec(['php', 'bin/console', 'lint:yaml', 'config', 'translations', '--parse-tags']),
        sylius_exec(['php', 'bin/console', 'lint:twig', 'templates']),
        sylius_exec(['php', 'bin/console', 'doctrine:schema:validate', '--skip-sync']),
    );
}

#[AsTask(name: 'cs', namespace: 'qa', description: 'Check the coding standards with ECS', aliases: ['cs'])]
function cs(
    #[AsOption(description: 'Fix the violations instead of only reporting them')]
    bool $fix = false,
): int {
    io()->section('Coding standards');

    return sylius_exec(['vendor/bin/ecs', 'check', ...($fix ? ['--fix'] : [])]);
}

#[AsTask(name: 'phpstan', namespace: 'qa', description: 'Run PHPStan', aliases: ['phpstan'])]
function phpstan(): int
{
    io()->section('PHPStan');

    return sylius_exec(['vendor/bin/phpstan', 'analyse', '--memory-limit=-1']);
}

/**
 * @param list<string> $arguments
 */
#[AsTask(name: 'phpunit', namespace: 'qa', description: 'Run PHPUnit', aliases: ['phpunit'], ignoreValidationErrors: true)]
function phpunit(#[AsRawTokens] array $arguments = []): int
{
    io()->section('PHPUnit');

    return sylius_exec(['vendor/bin/phpunit', ...$arguments]);
}

/**
 * @param list<string> $arguments
 */
#[AsTask(name: 'behat', namespace: 'qa', description: 'Run Behat (the scenarios without JavaScript by default)', aliases: ['behat'], ignoreValidationErrors: true)]
function behat(#[AsRawTokens] array $arguments = []): int
{
    io()->section('Behat');

    sylius_exec(['php', 'bin/console', 'doctrine:database:create', '--if-not-exists', '--env=test']);
    sylius_exec(['php', 'bin/console', 'doctrine:migrations:migrate', '--no-interaction', '--allow-no-migration', '--env=test']);

    return sylius_exec(['vendor/bin/behat', ...($arguments ?: [
        '--strict',
        '--format=progress',
        '--tags=~@javascript&&~@mink:chromedriver&&~@todo&&~@cli',
    ])]);
}

/**
 * The Sylius application registered in castor.php, if any.
 */
function find_sylius_service(): ?SyliusService
{
    foreach (collect_services() as $service) {
        if ($service instanceof SyliusService) {
            return $service;
        }
    }

    return null;
}

/**
 * Run a command in the builder container of the Sylius application and return its exit code.
 *
 * @param list<string> $command
 */
function sylius_exec(array $command): int
{
    $sylius = find_sylius_service() ?? throw new \RuntimeException('No Sylius application is registered in castor.php: run "castor sylius:init" first.');

    return docker_exit_code($command, $sylius->getBuilderServiceName());
}
