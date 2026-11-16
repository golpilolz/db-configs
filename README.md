# db-configs

Symfony bundle to store site settings as name/value pairs in the database and read them through a service.

```php
$config->set('site_title', 'My site');
$config->get('site_title');                         // 'My site'
$config->setArray('opening_hours', ['monday' => '9:00-18:00']);
$config->getArray('opening_hours');                 // ['monday' => '9:00-18:00']
```

## Documentation

- [Installation](docs/installation.md)
- [Usage](docs/usage.md)
- [Contributing](docs/contributing.md)

## License

MIT, see [LICENSE](LICENSE).
