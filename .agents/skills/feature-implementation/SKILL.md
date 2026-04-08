---
name: feature-implementation
description: Use when implementing a new feature or changing business behavior. Includes code, tests, static analysis, and final review.
---

## Goal
Implement feature with minimal changes, correct behavior, tests, and no regressions.

## Steps

1. Understand requirement
- Identify expected behavior
- Identify affected modules and boundaries

2. Locate minimal scope
- Do not refactor unrelated code
- Do not introduce new abstractions unless necessary

3. Implement change
- Keep logic in correct layer
- Do not add business logic to controllers
- Use constructor DI (no app() in business code)
- Validate external API payloads at boundaries
- Ensure all external HTTP calls have timeout

4. Tests
- Add or update tests if behavior changes
- Cover:
    - happy path
    - malformed payload (if integration)
    - failure scenarios

5. Run verification
- php artisan test
- vendor/bin/phpstan analyse

6. Fix all issues
- No failing tests
- No PHPStan errors

7. Review diff
   Check:
- no app() in business logic
- no create()->save() misuse
- no missing HTTP timeout
- no broad refactor
- minimal and scoped changes
- consistent typing

8. Completion
   Task is complete only if:
- tests pass
- phpstan passes
- no architectural regression
- diff is clean and minimal
