# Regression: the try guards under the 'pad' action

## Introduction

Regression test for `$padErrorTry`: the engine wraps its risky include points in
try/catch, and a throwable raised inside one is caught, reported into the page by the
'pad' error action with a CATCH prefix, and handled in place. One page per guarded point - the
page's own PHP and a lib include (the call guards), an application tag (the call guard
inside a level), a built-in handler (the level guard), a variable's pipe and a plain
expression (the eval guard) - each raising a real throwable. The pages that report
declare their HTTP 500; goNotOk shows the notOk option swallowing the catch, and the
level pages carry a line proving the page rendered on.

## Files

| File | Description |
|------|-------------|
| `call.php/pad` | The page's own PHP throws - caught at call/_try |
| `once/index.pad` + `once/_lib/boom.php` | A lib include throws - caught at call/_tryOnce |
| `tag.pad` + `_tags/boomtag.php` | An application tag throws - the call guard inside the level |
| `go.pad` | A built-in handler throws - caught at level/go |
| `goNotOk.pad` | The same throw, swallowed by notOk - the page answers 200 |
| `var.pad` | A variable's pipe throws - caught in the variable's guard chain |
| `eval.pad` | An expression throws - caught at eval/eval |
| `_config/config.php` | Turns the try guards on, error action 'pad', `_common` off |
