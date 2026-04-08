# Code Review Checklist

## Blocking issues
Reject changes if any of the following appear:

- service locator introduced into business logic
- external HTTP call without timeout
- external payload trusted without validation
- `create()->save()` or similar ORM misuse
- inaccurate public return types
- untyped public parameters in typed code
- business logic added to controllers
- unrelated refactoring mixed into a small task
- hidden side effects introduced

## Architecture checks
- Is logic in the correct layer?
- Did domain remain framework-free?
- Did the change preserve existing boundaries?
- Was a new abstraction actually necessary?
- Is dependency injection still explicit?

## Integration checks
- Are all external calls time-bounded?
- Is response shape validated before mapping?
- Does malformed payload fail cleanly?
- Is error handling consistent with nearby integrations?

## Typing checks
- Are new public methods typed?
- Are return types accurate?
- Are redundant casts removed?
- Is static analysis likely to pass?

## Persistence checks
- Are writes explicit and minimal?
- Any redundant save/update cycle?
- Any hidden ORM behavior introduced?
- Is the model lifecycle used correctly?

## Consistency checks
- Does the code follow existing project patterns?
- Was one area fixed while another similar area was left inconsistent?
- Does the diff look intentionally scoped?

## Final checks
Before approval:
- tests pass
- phpstan/larastan passes
- manual diff review completed
- no surprise architectural changes
