# Usage

Inject `Golpilolz\DBConfigs\Service\ConfigService` (it is autowired):

```php
use Golpilolz\DBConfigs\Service\ConfigService;

final class HomeController
{
    public function __construct(private readonly ConfigService $config)
    {
    }

    public function __invoke(): Response
    {
        $title = $this->config->get('site_title');
        $hours = $this->config->getArray('opening_hours', ['monday' => 'closed']);
        // ...
    }
}
```

## API

| Method | Returns | Notes |
|---|---|---|
| `get(string $key)` | `string` | `''` when the key does not exist. Read-only: never writes. |
| `set(string $key, string $value)` | `void` | Creates or updates the key. |
| `getArray(string $key, array $fallback = [])` | `array` | Decodes the stored JSON. Returns `$fallback` when the value is missing, empty, invalid JSON, or JSON that is not an array/object. |
| `setArray(string $key, array $data)` | `void` | Stores `$data` as JSON. Throws `\JsonException` if it cannot be encoded. |
| `all()` | `array<string, string>` | Every setting as `name => value`, most recently updated first, then by name. |

Values are always strings. Store numbers and booleans as strings, or group them in an array with `setArray()`.

## How writes work

`set()` (and `SiteVariableRepository::save()`) writes straight to the database with DBAL instead of going through the ORM unit of work:

- it does **not** flush your other pending Doctrine changes;
- two requests saving the same new key at the same time do not crash: the second one updates the row;
- a `SiteVariable` already loaded in the current request is refreshed with the new value.

Caveat: on PostgreSQL, if `set()` runs inside a transaction you opened and loses that race, the failed insert aborts your transaction.

Editing a `SiteVariable` entity yourself (for example from an admin form) and flushing it with the entity manager also works. `updatedAt` is refreshed automatically by the entity's `PrePersist` / `PreUpdate` callbacks.

## Repository

`Golpilolz\DBConfigs\Repository\SiteVariableRepository` is a regular Doctrine repository, useful for admin screens:

- `findOneByName(string $name): ?SiteVariable`
- `findAll()`: same order as `ConfigService::all()`
- `save(string $name, string $value): void`: what `ConfigService::set()` uses

## Performance

There is no cache: each `get()` / `getArray()` call runs one query. If you read many settings on every request, read them once with `all()`, or put your own cache in front of `ConfigService`.
