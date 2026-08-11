# Regression Test Application

## Introduction

The regression tests of the PAD framework: eight suites, every page in the tree asserted
by exactly one of them, with an overview that reports all eight on one line each and a
fresh build that regenerates everything a build owns.

## Structure

```
main/
├── _inits.pad/.php          # the menu every page carries - derived from the suite registry
├── _lib/regression.php      # the registry and the runner behind all eight suites
├── index.pad/.php           # the overview the application opens on - totals per suite
├── build.pad/.php           # the fresh build: wipe the results, run the eight suites
├── record.php               # records what a page answers now, for a covering suite's store
├── <suite>/index.pad/.php   # one overview page per suite, prose around shared includes
└── _include/                # the summary line and the test tables the overviews share
```

## The suites

Every suite fetches real pages over HTTP and compares each against a handwritten answer -
an exact body, an `HTTP <code>` with an optional `/pattern/` over the dump, or a lone
`/pattern/` for a page that draws. The registry in `_lib/regression.php` is the single
place a suite exists; the menu, the overview rows and the dispatch all derive from it.

**Pages** and **Common** are the handwritten suites: every test is a page of
`regression/pages` (with `_common` off) or `regression/common` (the pages that use it),
compared with the `name.txt` beside it. Nothing ever rewrites those answers.

**Framework** fetches every engine case under `regression/framework/<group>/` - one-line
asserts with a whitespace rule of their own.

**Regression**, **Sequence**, **Manual** and **Other** are the covering suites: the pages
stay in their own applications and only the predictions live in the store
`apps/regression/<suite>/`. Regression covers the self-testing family (and this runner
itself), one request at a time; Sequence and Manual cover those applications; Other covers
every application without a suite of its own, so a new application lands there as `new`
until its predictions are written. A failing row shows want and got in place, and a
covering suite's exact answers can be re-recorded from that row - patterns and HTTP
answers refresh only by hand.

## The build

Build - on the menu - wipes the suite results and the dumps, then runs the eight suites.
`DATA/reference` and `DATA/examples` are the develop application's artifacts: its harvest
pages gather them (the one crawl left) and they stand between builds, so the suites test
against the standing stores - the reference and manual applications render from them.
After changing what the stores hold, harvest in develop first. `ci.sh` gates on the seven
result files in `DATA/suites/`.
