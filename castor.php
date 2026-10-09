<?php

defined('CASTOR_USE_CHDIR') || define('CASTOR_USE_CHDIR', true);

use Castor\Attribute\AsContext;
use Castor\Context;

use function Castor\import;

import(__DIR__ . '/.castor/tasks');

#[AsContext(default: true)]
function default_context(): Context
{
    $repository = getenv('GITHUB_REPOSITORY');

    return new Context([
        // Every service exposing a UI gets a domain under it (https://app.test, …).
        'root_domain' => 'test',
        // Registry the Docker build cache is pulled from and pushed to (castor docker:push).
        // Defaults to the GitHub Container Registry of the repository in GitHub Actions.
        'registry' => $repository ? 'ghcr.io/' . strtolower($repository) : null,
    ]);
}

// "castor sylius:init" registers the Sylius application and its database below.
