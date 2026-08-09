# Regression2 - the pages suite

Every test here is an ordinary PAD page. It is fetched over HTTP with `&padInclude`, so it renders
bare, and what comes back is compared with the `.txt` beside it.

That is the whole point: a real request. A template rendered inside another running request - a
nested pass - has a variable scope of its own, so a page whose `.php` half leaves something for
its `.pad` half cannot be tested as one, and neither can anything that reads `$GLOBALS`, chains
`{page}`, runs a `{script}`, redirects, restarts, or ends the request. A fetched page can be all
of those.

They live in an application of their own because they are pages, not fixtures: nothing here is
support for something else, and keeping them apart means a test cannot accidentally read the
`_lib`, `_tags` or `_callbacks` of the harness that runs it.

This application switches `_common` off (`$padCommon = FALSE` in `_config/config.php`), so every
test here also proves it needs nothing but its own application. The pages that do use `_common` -
`{example}`, `{demo}`, `{table}`, the `{block}` snippet - are the same kind of test and live in
`regression3`, where `_common` is on.

## Running them

The overview is in the regression application, at `?pages/index` - it lists every test with what
it checks, and `Test` reruns them. `_lib/regression.php` there is the runner.

## Writing one

Add `name.pad`, `name.php` or both, and `name.txt` beside it. The `.txt` is written by hand and
the runner never rewrites it: an expectation the harness records for itself is a copy of whatever
the code did, not a prediction. A test with no `.txt` yet comes up `new` and the overview shows
exactly what came back.

Three forms of expectation:

| form | example | compared |
|------|---------|----------|
| a body | `Hello:abc` | exactly, trimmed at both ends |
| `HTTP <code>` | `HTTP 500` | the response code only |
| `HTTP <code>` + a pattern | `HTTP 500` then `/Pad error from PHP/` | the code, and `preg_match` over the body - for a page that exists to fail, so the right thing failing is asserted too |
| a pattern | `/^\d{4}-\d\d-\d\d/` | `preg_match`, for a page that answers differently every run |

A directory whose index renders the files beside it - a `{page}` chain - is one test, not one per
file. One that only links to them, as `regression3`'s `error/` does, is one test per file.

Give it a line in `getPagesWhatList()` in the runner so the overview can say what it checks.
