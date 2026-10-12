---
name: pad-bug-hunt
description: Hunting, proving and fixing bugs in PAD - the engine in pad/, the applications in apps/, the tooling in editors/. Reproducing a suspected bug with the PAD MCP tools, showing a fix against the old engine, writing the regression case in the suite it belongs to with the answer form that suite reads, and gating with the suites. Use when looking for a bug in PAD, checking whether something is one, or fixing one.
---

# Hunting a bug in PAD

The PAD MCP tools (server `pad`) answer from the engine of the checkout itself. Prefer them over
guessing from memory, and over hand-made scratch pages: a page left in `apps/` is crawled by the
Other suite and fails the gate as a test without an answer.

## Is it a bug?

- `pad_reference` - what the tag, function or option is documented to do. A behaviour the
  reference states is no bug, however odd; a reference that disagrees with the engine is one
  of the two being wrong.
- `pad_builtins` - the names the engine really has.

## Reproduce

- `pad_render` with `app` and `template` renders text that is in no file - bare, without the
  application's frame. `data` gives it variables without any PHP (`{"rows": [{"n": 1}]}` is
  iterable as `{rows}`), `query` gives request values. An answer that was not a 200 starts
  with its `HTTP <code>` line; a PAD error comes back with its tag and line.
- `pad_php` runs PHP in a booted application: the engine's library, the application's `_lib`,
  its configuration and database. A helper with an odd argument - an empty string, a
  negative number, a list where a value is meant, a multibyte string, `NULL` - answers what
  it returned, or the warning or `padError` it raised, as `ErrorException: PAD: ...`.
- `pad_trace` when the output is wrong and the levels the engine walked are needed.
- `pad_check` with only `app` renders every page of an application under the strict check.
- What the command line cannot show - response headers, cookies, a session, CSRF, CORS, the
  page cache, a POST - ask the running server: `curl -i 'http://localhost/pad/<app>/?<page>'`.

## Prove the fix

- `pad_compare` renders the same template or page - or runs the same snippet - on the commit
  `HEAD` (the old side, a snapshot from git) and on the working tree (the new side), and shows
  where they part. Before the fix both sides show the bug; after it only the old side does.
  It also lists the uncommitted engine changes the working tree holds: another session may
  be working in the same checkout, and its changes run on the new side too.
- A regression case must fail on the old engine. The applications are the working tree's on
  both sides, so a case not committed yet runs on the old engine as well:
  `pad_compare app=regression/framework page=tags/<case> bare=true` - the old side must differ
  from the case's answer, the new side must equal it.

## The regression case

Write the case before the fix, see it fail, then fix. A case is named for what it asserts, as a
sentence: `a_copy_pair_takes_off_the_indent_its_lines_share`.

| What the bug is in | Where the case goes | Its answer, `<name>.txt` beside it |
|---|---|---|
| A tag, function, option, property, expression - output of a template | `apps/regression/framework/<group>/<name>.pad`, a `.php` beside it for variables. Groups: `tags`, `functions`, `options`, `properties`, `expressions`, `data`, `helpers`, `escaping`, ... | The output with every line break and the whitespace after it removed - one line - or a `/regex/` for a page that draws |
| A request as a whole - `.php` and `.pad` halves, `$GLOBALS`, `{page}` chains, redirects, forms, flush, sessions | `apps/regression/pages/<group>/<name>.pad`/`.php` (`_common` off) or `apps/regression/common/...` (on) | The exact body, trimmed; `HTTP <code>` with a `/pattern/` on its second line; or a lone `/pattern/` |
| An error the engine must report | `apps/regression/errors/<dir>/<name>.pad` - `syntax/`, `eval/`, `error/`, `helpers/`, `handlers/`, `source/` | `HTTP 500` and a `/pattern/` over the message |
| A page of an application - `manual`, `showcase`, `demo`, ... | Nothing to add: a covering suite holds its answer in `apps/regression/<suite>/` (`other/<app>/<page>.txt`) | A changed page fails there; check every difference, then re-record it from the suite's overview (`regression/main/?<suite>/index`, the row's record link) |

A Pages or Common case also gets a line in `getSuiteWhatList()` in
`apps/regression/main/_lib/what.php`.

`pad_case app=<application> page=<page>` runs one test exactly as its suite does - fetched from
the running server, judged by the runner's own comparison - and answers `ok`, `FAILED` with
want against got, or `new` with what came back (for a handwritten answer that is the text to
check, not to copy).

## The gate

- `pad_test suite=<suite>` runs one suite (seconds) - for the suite the case is in.
- `pad_test` runs `./ci.sh`, every suite and the tooling checks. A commit needs it green.
- Both write `DATA/suites/`: a `./ci.sh` run started elsewhere at the same moment fails as
  "another run's". Check for one (`ps aux | grep ci.sh`) before starting a run.
- A new tag, function or option name changes the editor completion lists
  (`php editors/generate.php`) and the reference index's answer in
  `apps/regression/other/reference/index.txt` - check that only the new name was added.
- The web server's opcache checks a file for changes only every few seconds
  (`opcache.revalidate_freq`): a fetch right after an edit can still run the old code. The
  command-line tools - `pad_render`, `pad_php`, `pad_compare` - always run what is on disk.

## Committing

The checkout may be shared with other sessions or a build: commit by path
(`git commit -m "..." -- <paths>`), never `git add -A`, and look at `git status` first. Follow
the repository's commit style - `git log` shows it: one descriptive sentence of what happens now
and what happened before.
