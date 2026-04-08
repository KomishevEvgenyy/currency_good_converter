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
