---
name: code-review
description: Use to audit code before merging. Focuses on correctness, architecture, and real issues only.
---

## Goal
Perform strict review like a senior engineer.

## Rules
- Do not suggest improvements unless necessary
- Ignore style preferences
- Focus only on real issues

## Review checklist

### Critical issues
- service locator (app()) in business logic
- missing timeout on external HTTP
- external payload used without validation
- incorrect ORM usage (e.g. create()->save())
- broken contracts (API, return types)
- logic placed in wrong layer

### Moderate issues
- inconsistent error handling
- partial validation of payloads
- inaccurate typing
- duplication of knowledge
- unclear responsibilities

### Minor issues
- redundant casts
- inaccurate return types
- minor inconsistencies

## Architecture checks
- logic is in correct layer
- domain has no framework leakage
- DI is explicit
- no unnecessary abstractions

## Integration checks
- HTTP calls have timeout
- payload validated before mapping
- malformed payload handled explicitly

## Consistency checks
- same patterns used across codebase
- no mixed approaches (DI vs app())
- no partial fixes

## Output format

Return:
1. Issues list:
    - File
    - Problem
    - Severity (Critical / Moderate / Minor)
    - Short explanation

2. Final verdict:
- APPROVE
  or
- REJECT

3. If REJECT:
- list blocking issues only
