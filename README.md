# Sylius Starter skeleton

Start a new [Sylius](https://sylius.com) project, ready to develop, in one command.

The skeleton is a thin shell around [Sylius Starter](https://github.com/sylius-starter/sylius-starter):
[Castor](https://castor.jolicode.com) installs Sylius-Standard, its Docker stack (FrankenPHP, PostgreSQL, HTTPS
router), the assets and the fixtures, then hands you the tasks to shape your store.

## Requirements

- 🐳 [Docker](https://docs.docker.com/get-docker/) with Compose
- 🦫 [Castor](https://castor.jolicode.com/getting-started/installation/)

## 🚀 Create your project

```shell
git clone --depth=1 https://github.com/sylius-starter/skeleton.git my-shop
cd my-shop
rm -rf .git && git init

castor sylius:init
```

> On GitHub, **Use this template** gives you a fresh repository without cloning the history.

`castor sylius:init` asks a few questions (application name, PHP version, domain, database, theme, payment
gateways…), then builds and starts everything. Once done, it removes the skeleton-only files: this README is
replaced by the one of your project, and `.github.dist/` becomes your CI.

Every question can be answered upfront, for an unattended install:

```shell
castor sylius:init --no-interaction \
    --with-name=app \
    --with-domain=my-shop.test \
    --with-database=postgres \
    --with-theme=canvas \
    --with-payment-gateways=stripe
```

| Option                    | Description                                   | Default      |
|---------------------------|-----------------------------------------------|--------------|
| `--with-name`             | Name of the application (and of its tasks)    | `app`        |
| `--with-directory`        | Directory of the application                  | the name     |
| `--with-version`          | PHP version                                   | `8.5`        |
| `--with-mode`             | Runtime: `frankenphp` or `fpm`                | `frankenphp` |
| `--with-domain`           | Domain of the store                           | `app.test`   |
| `--with-sylius-version`   | Sylius-Standard version                       | latest       |
| `--with-database`         | `postgres`, `mysql`, `mariadb` or `none`      | `postgres`   |
| `--with-theme`            | `default`, `aurora`, `blush`, `canvas`, `lagoon`, `prompt_dark`, `prompt_light` or `volt` | `default` |
| `--with-payment-gateways` | `mollie`, `paypal`, `stripe` (repeat the option for several) | all |

`castor docker:service:install` (without argument) prints the options of every installable service.

## What's inside

| File                    | Role                                                                    |
|-------------------------|-------------------------------------------------------------------------|
| `castor.php`            | Your stack: Castor context, and the services `sylius:init` registers    |
| `castor.composer.json`  | The Castor plugins of the project (`sylius-starter/sylius-starter`)     |
| `.castor/tasks/init.php`| The `sylius:init` task, removed once the project is created             |
| `.castor/tasks/qa.php`  | `castor qa`: audit, linters, ECS, PHPStan, PHPUnit (and Behat)          |
| `.github.dist/`         | CI of the project: quality checks, Docker build cache, Dependabot       |
| `README.dist.md`        | README of the project                                                   |
| `AGENTS.md`             | Guidance for the AI agents working on the project                       |

## Contributing

The `.github/` workflow of this repository creates a project with `castor sylius:init` and runs its quality checks.
The tasks themselves (installer, themes, plugins, payment gateways…) live in
[sylius-starter/sylius-starter](https://github.com/sylius-starter/sylius-starter).

## License

[MIT](LICENSE)
