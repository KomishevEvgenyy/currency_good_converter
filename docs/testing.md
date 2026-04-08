# Testing and Verification

## Required commands

Run after every meaningful change:

```bash
php artisan test
vendor/bin/phpstan analyse
```

When tests must be added or updated
Tests are required if:
- business behavior changed
- public API behavior changed
- external integration handling changed
- validation behavior changed
- queue/job side effects changed
- cache semantics changed

Testing expectations
Unit tests
Use for:
- domain logic
- deterministic conversion logic
- small isolated classes
- decorators with explicit doubles

Avoid framework boot unless needed.
Feature tests
Use for:
- HTTP endpoints
- request validation
- JSON contract checks
- queue dispatch checks
- framework integration behavior

Integration boundaries
External APIs must be tested with fake/mock responses for:
- valid payload
- malformed payload
- missing keys
- timeout/failure path

Static analysis
Larastan/PHPStan is required to catch:
- inaccurate return types
- missing parameter types
- redundant casts
- unreachable types
- framework misuse visible to analysis

Manual review
After tools pass, inspect the diff manually and verify:
- no regression from DI to service locator
- no missing timeout
- no broad unrelated refactor
- no hidden side effects


