---
name: refactor-pass
description: Use when refactoring existing code without changing behavior. Ensures safety, minimal diff, and no regressions.
---

## Goal
Improve code without changing behavior.

## Steps

1. Define scope
- Identify exact code to refactor
- Do not expand scope

2. Preserve behavior
- No logic changes
- No contract changes (API, return values, exceptions)

3. Apply improvements
   Allowed:
- improve structure
- improve typing
- fix DI usage
- remove redundancy
- improve readability

Not allowed:
- new architecture
- new layers
- speculative abstractions

4. Respect rules
- no app() in business logic
- keep domain framework-free
- do not move logic across layers unless required

5. Verification
- php artisan test
- vendor/bin/phpstan analyse

6. Fix issues
- all tests must pass
- no new PHPStan issues

7. Diff review
   Check:
- behavior unchanged
- no hidden side effects
- no unrelated changes
- no excessive code movement

8. Completion
   Refactor is complete only if:
- behavior identical
- tests pass
- phpstan passes
- diff is minimal
