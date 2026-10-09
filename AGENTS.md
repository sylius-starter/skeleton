# Agent guidance

This is a [Sylius](https://sylius.com) project driven by [Castor](https://castor.jolicode.com) and
[Sylius Starter](https://github.com/sylius-starter/sylius-starter). If `castor.php` registers no Sylius service yet,
the project is still a skeleton: run `castor sylius:init` first.

## Layout

- `castor.php`: the stack (Castor context and Docker services). Edit it rather than `compose.generated.yaml`,
  which is regenerated on every Castor run.
- `castor.composer.json`: the Castor plugins. Change them with `castor composer require|remove`.
- `.castor/tasks/`: the tasks of the project (`castor qa`…).
- `app/`: the Sylius application (Sylius-Standard).

## Running commands

Never run PHP, Composer or Node on the host: everything runs in the containers, through Castor.

```bash
castor app:symfony cache:clear            # Symfony console
castor app:composer require vendor/pkg    # Composer
castor app:bash                           # shell in the PHP container
castor app:db:migrate                     # database migrations
castor list                               # every task
```

## Prefer the Sylius Starter tasks

To add or remove a plugin, a payment gateway or a theme, hide admin menu items or enable B2B features, use the
`castor sylius:*` tasks instead of editing the application by hand: they install, configure, migrate and clean up
in one deterministic step. `castor list sylius` lists them.

## Before handing over

Run `castor qa` (audit, linters, ECS, PHPStan, PHPUnit) and fix what it reports; `castor qa:cs --fix` fixes the
coding standards.
