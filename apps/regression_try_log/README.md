# Regression: the try guards under the 'log' action

## Introduction

The same seven pages as regression_try_pad, run under the 'log' error action: a
throwable at a guarded include point is caught, written to the log, and the page
answers a clean 200 with all of its own content - the quiet side of the try guards.
Every page here renders completely; what went wrong is in the error log alone.

## Files

| File | Description |
|------|-------------|
| `call.php/pad` | The page's own PHP throws - logged, the page renders |
| `once/index.pad` + `once/_lib/boom.php` | A lib include throws - logged, the page renders |
| `tag.pad` + `_tags/boomtag.php` | An application tag throws - logged, the page renders |
| `go.pad` | A built-in handler throws - logged, the page renders |
| `goNotOk.pad` | The same throw, swallowed by notOk before any report |
| `var.pad` | A variable's pipe throws - logged, the page renders |
| `eval.pad` | An expression throws - logged, the page renders |
| `_config/config.php` | Turns the try guards on, error action 'log', `_common` off |
