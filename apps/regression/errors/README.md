# Regression: the Errors suite

## Introduction

The tests that fail on purpose: the strict syntax checks, the expression evaluator's
reports, the pages that end a request, and the error handler family - every case here
answers an expected `HTTP 500`, asserted by a `/pattern/` over the message. The
application declares the `boot` error action, so an expected failure is answered as the
lean JSON dump: no heavy error page, nothing written under `DATA/dumps`, and the same
shape for every requester. `_common` is off, like the Pages suite these cases grew in.

| Directory | What fails there |
|-----------|------------------|
| `syntax/` | The strict syntax check - one case per authoring mistake it names |
| `eval/` | The expression evaluator's reports - brackets, operators, unknown names |
| `error/` | The pages that end a request - {error}, {dump}, {exit}, a throw, on demand |
| `handlers/` | The handler family - a warning, an error, an exception, a shutdown, each from a tag and from a page |
