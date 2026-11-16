# Installation

Requirements: PHP 8.3+, Symfony 7.4 or 8, Doctrine ORM 3.6+ with DoctrineBundle 2.18+ or 3.2+.

## 1. Install the package

```bash
composer require golpilolz/db-configs
```

## 2. Register the bundle

Symfony Flex usually adds it for you. If `config/bundles.php` does not list it, add it:

```php
return [
    // ...
    Golpilolz\DBConfigs\GolpilolzDBConfigs::class => ['all' => true],
];
```

The bundle has no configuration of its own.

## 3. Map the entity

The bundle ships one Doctrine entity, `Golpilolz\DBConfigs\Entity\SiteVariable`, mapped with attributes.

With `auto_mapping: true` (the default in Symfony's Doctrine recipe), nothing else is needed: DoctrineBundle finds the bundle's `Entity/` directory.

Otherwise, add the bundle to your mappings in `config/packages/doctrine.yaml`:

```yaml
doctrine:
    orm:
        mappings:
            GolpilolzDBConfigs: ~
```

## 4. Create the table

```bash
bin/console doctrine:migrations:diff
bin/console doctrine:migrations:migrate
```

The table has three columns: `name` (string, primary key), `value` (text) and `updatedAt` (datetime). The table and column names follow your naming strategy: with Symfony's recipe (`underscore_number_aware`) they are `site_variable` and `updated_at`.

Next: [Usage](usage.md).
