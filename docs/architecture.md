# Architecture

## Project style
This project uses pragmatic layered architecture with a modular Currency subsystem.

The code should optimize for:
- correctness
- explicitness
- testability
- low regression risk

Not for:
- maximum abstraction
- framework ceremony
- speculative flexibility

## Layers

### Domain
Responsibilities:
- business rules
- value objects
- domain services
- pure conversion logic

Rules:
- no Laravel facades
- no container access
- no Eloquent
- no request/response concerns

### Application
Responsibilities:
- use-case orchestration
- interaction between controllers and domain
- resources / response translation

Rules:
- thin orchestration
- no heavy business logic in controllers
- keep contracts explicit

### Infrastructure
Responsibilities:
- HTTP integrations
- cache
- persistence details
- framework-specific wiring

Rules:
- validate external payloads
- define timeouts
- fail with controlled exceptions
- keep framework coupling here, not in domain

### Composition root
Responsibilities:
- build object graph
- choose implementations from config
- wire decorators/providers/contracts

Rules:
- service locator behavior may exist only here if needed
- do not leak container access into business code

## Current design expectations

### Currency subsystem
- contract-driven reader boundary is acceptable
- fallback and cache decorators are acceptable
- provider selection from config is acceptable
- external provider payloads must be validated before mapping

### Catalog flow
- catalog retrieval is an integration boundary
- external catalog payload must be validated
- price conversion must not live in the controller

## Design bias
Prefer:
- explicit orchestration
- narrow responsibilities
- minimal abstractions

Avoid:
- fat facades
- generic helper layers
- “framework inside domain”
- abstractions added only because they look clean

## Integration Boundary Design

External API integrations must remain explicit, readable, and testable.

### Responsibility split

Integration classes (e.g. repositories using HTTP APIs) must NOT combine all logic into one method.

A typical flow should be:

1. Perform HTTP request
2. Validate response shape
3. Parse and map into domain DTOs

If this logic becomes non-trivial, it must be split.

### When to extract a mapper/parser

Extract a dedicated mapper/parser if:

- payload validation is more than trivial
- mapping logic contains conditionals or filtering
- multiple fields require transformation
- method readability degrades
- method mixes validation + transformation + orchestration

### Expected structure

Repository:
- performs HTTP call
- delegates payload handling
- returns domain objects

Mapper/Parser:
- validates payload
- transforms data
- throws controlled exceptions on malformed input

### Fail-fast rule

Integration boundaries must not:
- silently skip invalid data
- partially accept malformed payloads
- fabricate missing values

If payload is invalid:
→ fail immediately with a controlled exception

### Anti-patterns (must be avoided)

- large “do-everything” methods (HTTP + validation + parsing + mapping)
- `continue` on invalid payload rows instead of failing
- mixing orchestration and transformation logic
- hidden data normalization

### Goal

Keep integration logic:
- explicit
- predictable
- easy to audit
- easy to test in isolation

### Method size heuristic

If a method:
- exceeds ~30–40 lines
- contains multiple logical stages
- mixes different responsibilities

It must be reviewed for extraction.
