# Regression Test Application

## Introduction

Three kinds of regression test for the PAD framework, each answering a different question.

## Structure

```
regression/
├── _inits.pad/.php   # The menu every page carries, and the Test link
├── _lib/
│   ├── cases.php     # the sandbox runner
│   └── pages.php     # the pages runner
├── index.php         # opens on ?sandbox/index
├── all.pad/.php      # the crawl: every page of every application
├── ok.php            # accepts a crawl difference as the new baseline
├── show/             # what one crawled page rendered
├── sandbox/          # the sandbox suites
│   ├── index.pad/.php
│   ├── <group>.pad/.php
│   └── _cases/<group>/*.php
└── pages/            # the pages suite
    ├── index.pad/.php
    └── <name>.php, <name>.pad, <name>.txt
```

## The three kinds

**Sandbox** - `?sandbox/index`. A case is `[ name, template, expected ]`: the template is
rendered inside the running request and compared with the output it states. Fast, self-contained,
and the right place for anything about the engine itself - tags, options, expressions, functions,
properties, the sequence subsystem. See `sandbox/_cases/README.md` for the format and for what
this kind of test cannot check.

**Pages** - `?pages/index`. A test is an ordinary page of this application: `name.php` with an
optional `name.pad`. It is fetched over HTTP with `&padInclude`, so it renders bare, and compared
with `name.txt` beside it. That makes it a real request, which is the one thing a sandbox case is
not: a page's `.php` half is in scope for its `.pad` half, and a page variable is a global. Use it
for anything that needs those.

`name.txt` is written by hand and the runner never rewrites it. An expectation the harness records
for itself is not a prediction, and a change to it would be recorded rather than reported. A test
with no `.txt` yet comes up `new`, and the overview shows exactly what came back.

**All** - `?all`. Crawls every page of every application, compares each against the copy in
`DATA/regression/`, and reports ok / new / warning / error / random / empty. This one catches
what neither of the others is looking at, at the cost of saying only "this changed".

## Running

Every page carries a **Test** link. On a sandbox group page it reruns that group, on
`?sandbox/index` all of them, on `?pages/index` the pages suite, and on `?all` the suites and then
the crawl. A page load never runs anything - it reads what the last run left behind.

Sandbox and pages runs are kept in `DATA/suites/`, crawl baselines in `DATA/regression/`.

## Access

Via web browser: `http://server/regression/`
