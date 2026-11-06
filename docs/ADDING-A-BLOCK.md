# Adding a block

1. Add a class in `app/Builder/Blocks` implementing `BlockDefinition`.
2. Define type, label, category, defaults, nesting rules, and validation rules.
3. Add one escaped Blade renderer under `resources/views/builder/blocks`.
4. Register the class once in `AppServiceProvider`.
5. Add registry, validation, preview, and published-render tests.

```php
$registry->register(new CountdownBlock());
```

Never add parallel type switch statements to the editor, preview, and publisher.
