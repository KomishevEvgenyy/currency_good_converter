# AGENTS.md

## Purpose
This repository must prioritize correctness, simplicity, consistency, and reviewability over cleverness or abstraction purity.

The goal is not to produce the most abstract architecture.
The goal is to produce explicit, testable, maintainable code with minimal regression risk.

## Priority order
When rules conflict, follow this order:

1. Correctness
2. Simplicity
3. Consistency with existing architecture
4. Testability
5. Static-analysis cleanliness
6. Extensibility
7. Abstraction purity

## Core rules

### Correctness first
- Happy-path only code is not acceptable at integration boundaries.
- External APIs, queue inputs, cache values, and DB records are untrusted boundaries.
- Fail with controlled exceptions instead of TypeError, undefined index, or silent partial behavior.

### Keep it simple
- Prefer KISS over abstraction layering.
- Prefer explicit code over clever code.
- Do not introduce abstractions for hypothetical future needs.

### YAGNI
- Do not add factories, resolvers, interfaces, services, mappers, DTOs, or decorators unless they solve a real current problem.
- Do not generalize for scenarios not required by the task or existing codebase.

### DRY, but pragmatically
- Avoid duplication of knowledge, not every repeated line.
- A few repeated explicit lines are better than a misleading abstraction.
- Do not merge unrelated concerns only to reduce duplication.

### SOLID, but pragmatic
- One class should have one clear reason to change.
- Prefer constructor injection.
- Depend on contracts only where real variability exists.
- Do not create interfaces for single stable implementations unless there is a clear boundary reason.

## Architecture rules for this project

### Pragmatic DDD
- Domain code must stay free from Laravel framework dependencies.
- Infrastructure owns HTTP, cache, framework integrations, and persistence details.
- Application layer orchestrates use-cases.
- Controllers must stay thin.

### No framework leakage into domain
Do not use these inside domain code:
- `app()`
- facades
- `request()`
- Eloquent models
- container lookups
- hidden runtime service resolution

Use explicit dependencies instead.

### Composition root discipline
- Service providers may assemble dependencies.
- Config-driven provider selection is allowed at the composition root.
- Resolver/helper classes are acceptable only if they stay confined to the composition root and do not leak service-locator behavior into business logic.

### Thin controllers
Controllers must not contain:
- business rules
- persistence logic
- complex mapping
- integration payload validation
- orchestration that belongs in services

Controllers may:
- validate request input
- call application services
- return resources/responses

### Integration boundary validation
For every external API:
- define a timeout
- validate response shape before mapping
- do not trust 200 responses blindly
- fail fast with controlled exceptions on malformed payloads

### Mapping discipline
- Simple mapping may stay close to the integration if it is small and explicit.
- If validation/mapping logic becomes noisy or harms readability, extract it into a dedicated mapper/parser.
- Do not extract mappers mechanically for trivial one-liners.

## Code quality rules

### Typing
- New public methods must be typed.
- Return types must be accurate.
- Do not use false union return types.
- Remove redundant casts.
- Keep code friendly to PHPStan/Larastan.

### Eloquent usage
- Do not use incorrect ORM lifecycle patterns like `create()->save()`.
- Avoid redundant queries and unnecessary persistence calls.
- Side effects must be explicit and minimal.

### HTTP usage
- Every external HTTP call must set a timeout.
- Error handling must be consistent across integrations.
- Response payloads must be validated before key access.

### Logging
- Log only meaningful operational events.
- Do not replace proper exception flow with logging.
- Logging should support diagnosis, not hide failures.

### Cache
- Cache semantics must be explicit.
- Cache must not silently hide correctness issues unless this is a conscious documented tradeoff.
- Invalidation logic must remain obvious.

## Change management rules

### Minimal diffs
- Keep changes scoped.
- Do not refactor unrelated code.
- Do not rename, move, or restructure files unless required.

### Preserve contracts
- Do not change public API shape unless explicitly required.
- Do not change route behavior, JSON contracts, or error semantics unless the task requires it.

### Reviewability first
- Write code that is easy to review.
- Avoid surprise abstractions.
- Avoid broad rewrites for small fixes.

## Completion rules

A task is not complete until all of the following are true:

1. Code changes are implemented
2. Tests are added or updated when behavior changed
3. Existing tests pass
4. PHPStan/Larastan passes
5. No obvious architectural regression was introduced
6. Diff was reviewed for consistency and unnecessary changes

## Required verification after every meaningful code change

Run:
- `php artisan test`
- `vendor/bin/phpstan analyse`

If the task changes behavior, also:
- add or update tests
- manually inspect the diff

## Blocking mistakes
Never submit code with:
- `app()` introduced into business logic
- missing timeout on external HTTP
- trusting external payload shape without validation
- inaccurate public return types
- untyped public parameters in otherwise typed code
- redundant ORM persistence like `create()->save()`
- broad refactors unrelated to the requested task

## Required workflows
Use repository skills for repeatable work:
- feature-implementation
- refactor-pass
- code-review

When a task matches one of these workflows, follow the skill instead of improvising.
