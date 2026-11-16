# Contributing

## Layout

The package root is the PSR-4 root (`Golpilolz\DBConfigs\` → `./`, there is no `src/`):

- `GolpilolzDBConfigs.php`: the bundle class; loads `Resources/config/services.php`.
- `Resources/config/services.php`: autowires every class in `Repository/` and `Service/`. Put a new service in one of those directories, or register it there.
- `Entity/`, `Repository/`, `Service/`: the code.
- `Tests/`: PHPUnit tests (`Golpilolz\DBConfigs\Tests\`).

## Tests

```bash
composer install
vendor/bin/phpunit
vendor/bin/phpunit --filter testGetArrayDecodesJsonValue   # one test
```

Without a local PHP, use Docker:

```bash
docker run --rm -u $(id -u):$(id -g) -e COMPOSER_HOME=/tmp/c -v "$PWD":/app -w /app composer:2 \
  sh -c 'composer install -q && vendor/bin/phpunit'
```

The repository tests run against an in-memory SQLite database, so the `pdo_sqlite` extension is required. The other tests use mocks.

CI (GitHub Actions) runs `composer validate --strict` and the test suite on PHP 8.3 and 8.4.
