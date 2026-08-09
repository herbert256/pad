# Regression Test Application

## Introduction

Four kinds of regression test for the PAD framework, each answering a different question, with
an overview that reports all of them on one line each.

## Structure

```
regression/
├── _inits.pad/.php          # the menu every page carries: Index Pages Common Framework Scan | Test
├── _lib/regression.php      # the runner behind all four kinds
├── index.pad/.php           # the overview the application opens on - totals per kind
├── pages/index.pad/.php     # the Pages suite overview     - its tests live in apps/regression2/
├── common/index.pad/.php    # the Common suite overview    - its tests live in apps/regression3/
├── framework/index.pad/.php # the Framework suite overview - its tests live in apps/regression4/
├── scan/index.pad/.php      # the crawl of every page of every application
├── ok.php                   # accepts what a page renders now as its baseline
└── show/                    # what one crawled page renders, against its stored copy
```

## The four kinds

**Pages** - `?pages/index`. A test is an ordinary page of the `regression2` application, fetched
over HTTP with `&padInclude` so it renders bare, and compared with the `name.txt` beside it.
Being a real request means a page's `.php` half is in scope for its `.pad` half, a page variable
is a global, and `{page}`, `{restart}`, `{script}` and callbacks behave as they do in production.
`regression2` runs with `$padCommon` switched off, so every test there also proves it needs
nothing but its own application.

**Common** - `?common/index`. The same kind of test, from the `regression3` application, for the
pages that use `_common`: `{example}`, `{demo}`, `{table}`, the `{block}` snippet, the menu, the
colouring functions in `_common/_lib/`. Which application a page is in is itself the assertion.

**Framework** - `?framework/index`. The engine cases, from the `regression4` application: tags,
options, expressions, functions, properties, the sequence subsystem. Every case is a page of its
own in a group directory - the `.pad` its template, the `.txt` the outcome beside it, an optional
`.php` its variables - so a case gets the isolation a request brings by construction. Nine
hundred requests a run, which is why Test runs and a page load only reads.

`name.txt` is written by hand and the runner never rewrites it. Three spellings: a body compares
exactly; `HTTP <code>` asserts the response code, with an optional second line holding `/a
pattern/` the body must match too, so a page that exists to fail asserts the right thing failing;
a file that is one `/pattern/` is matched against the whole body, for a page that draws. A test
with no `.txt` yet comes up `new`, and the overview shows exactly what came back.

**Scan** - `?scan/index`. Crawls every page of every application, compares each against the copy
in `DATA/regression/`, and reports ok / expected / new / warning / error / random / empty.
`expected` is a page that fails on purpose - a 500 its suite expectation declares - counted
but kept off the list of what needs looking at; a page as empty as its stored copy is simply
`ok`. This kind catches what none of the others is looking at, at the cost of saying only
"this changed". After a deliberate change the page offers to accept every warning as the new
baselines in one step; read the list first, a page that keeps coming back is telling you
something.

## Running

Every page carries the menu, and where there is something to run, a **Test** entry: on the index
it runs everything, on a suite page that suite, and on the scan page the suites and then the
crawl. A page load never runs anything - it reads what the last run left behind, and the index
shows when that was.

Suite runs are kept in `DATA/suites/`, crawl baselines in `DATA/regression/`.

For a machine, `ci.sh` in the repository root runs everything, prints one line per suite, and
exits nonzero when anything failed - the shape a git hook or a CI step can act on. The scan's
warnings are reported but are not part of the verdict, since they compare against baselines
another machine may not have.

## Access

Via web browser: `http://server/regression/`
