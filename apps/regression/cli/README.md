# Regression: the pad command

## Introduction

Regression test for `apps/cli/pad`, the command-line tool. The verdict runs the command in
child processes and asserts each of its promises: `render` writes a page - with request
values given as `name=value` - and refuses an unknown application; `new` makes an
application and a page in a scratch `PAD_HOME` under `DATA/` (its engine and `_common`
linked to the real ones), refuses to overwrite, and the new page renders; `lint` lists the
pages of `lintme/`, the broken one with its template position and a near name, and
leaves its bracketed route `[id]` out; `serve`
answers a page from the server it starts on a free port; `export` writes the
`regression/site` fixture as static files, its links rewritten - from a subdirectory too -
its assets copied; `test` runs the scratch application's `_tests` - a pass, a failing
`{assert}`, a test without an answer that `--record` then writes - and those of
`regression/site`, whose test pages no URL reaches. The crawl compares the verdict,
so a command that stops behaving turns its yes into a NO.

## Files

| File | Description |
|------|-------------|
| `index.php/pad` | Runs the commands and states the verdicts |
| `verdict.php/pad` | The same check run on every load, so the crawl holds the command to it |
| `sample.php/pad` | The page `render` and `serve` are tested on |
| `lintme/` | A good page, a broken one and a bracketed route, for `lint` |
| `_lib/cli.php` | Runs the command in a child process; starts and stops `serve` |
| `_config/config.php` | `_common` off |
