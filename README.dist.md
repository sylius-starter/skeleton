# My Sylius store

A [Sylius](https://sylius.com) store bootstrapped with [Sylius Starter](https://github.com/sylius-starter/sylius-starter).

## Requirements

- 🐳 [Docker](https://docs.docker.com/get-docker/) with Compose
- 🦫 [Castor](https://castor.jolicode.com/getting-started/installation/)

## Getting started

```shell
castor build          # build the Docker images
castor up             # start the stack
castor app:install    # install the PHP dependencies
castor app:db:migrate # run the database migrations
castor sylius:fixtures
```

`castor docker:about` lists the URLs of the project. The admin panel lives under `/admin`
(login `sylius`, password `sylius` with the fixtures).

The Sylius application lives in [`app/`](app/), the stack is described in [`castor.php`](castor.php).

## Everyday tasks

| Task                                    | Description                                    |
|-----------------------------------------|------------------------------------------------|
| `castor up` / `castor stop`             | Start / stop the stack                         |
| `castor app:bash`                       | Open a shell in the PHP container              |
| `castor app:symfony <command>`          | Run a Symfony console command                  |
| `castor app:composer <command>`         | Run Composer                                   |
| `castor app:db:migrate`                 | Run the database migrations                    |
| `castor sylius:fixtures`                | Load the fixtures                              |
| `castor docker:logs`                    | Show the logs of the stack                     |
| `castor list`                           | Every available task                           |

## Shape your store

| Task                                         | Description                                      |
|----------------------------------------------|--------------------------------------------------|
| `castor sylius:add <plugin>…`                | Install Sylius plugins (CMS, invoicing, refund…) |
| `castor sylius:remove <plugin>…`             | Remove Sylius plugins                            |
| `castor sylius:theme:setup <theme>`          | Switch the storefront theme                      |
| `castor sylius:payment-gateways:setup <gw>…` | Set up Mollie, PayPal, Stripe…                   |
| `castor sylius:menu:remove <item>…`          | Hide admin menu items                            |
| `castor sylius:b2b:enable <feature>…`        | Enable B2B features                              |
| `castor sylius:import:*`                     | Import a catalog (AI generated or from a site)   |
| `castor sylius:upsun:check`                  | Check the Upsun deployment configuration         |

See the [Sylius Starter documentation](https://github.com/sylius-starter/sylius-starter#-available-commands)
for every option.

## Quality

```shell
castor qa             # audit, linters, coding standards, PHPStan, PHPUnit
castor qa:cs --fix    # fix the coding standards
castor qa:behat       # Behat scenarios (non-JavaScript ones by default)
```

The same checks run on every pull request (`.github/workflows/ci.yml`). Pushes to `main` refresh the Docker build
cache in the GitHub Container Registry (`.github/workflows/cache.yml`), which speeds up the CI.
