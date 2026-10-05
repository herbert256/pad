# Development Tools

## Introduction

Development utilities for building, testing, and debugging PAD applications.

## Structure

```
develop/
├── _include/        # Development includes
├── _inits.pad       # Development wrapper
├── _lib/            # Development libraries
├── benchmark/       # Benchmark: index (run, keep, compare), history (chart), check (for ci.sh)
├── sequence/        # Sequence development tools
├── index.pad/.php   # Development dashboard
├── build.php        # Build utilities
├── clean.php        # Cleanup utilities
├── errors.pad/.php  # The error dumps a crawl left behind, one line per dump
├── links.pad/.php   # Every literal link in every application's templates, checked like $padCheckOutput does
├── coverage.pad/.php # Template coverage: start/stop a recording around a suite run, the report, a marked template
├── replay.pad/.php  # Replay recorded GET requests against the current code, accept or delete what changed
├── examples.php     # Example generator
├── nuts.pad/.php    # Miscellaneous utilities
├── reference.php    # Reference builder
└── regression.php   # Starts a fresh build of the regression suites (with go=)
```

## Features

- **Build tools**: Compile and prepare PAD applications
- **Benchmarks**: `?benchmark` times every page outside the regression family through its
  PAD-Stats header, keeps the run in `DATA/benchmark/` with its commit, and lists the pages
  more than 25% slower than their median over the last five runs (a page named is timed
  again on its own first, and must have lost 5 ms too); `?benchmark/history` charts the
  kept runs; `?benchmark/check` is the plain-text verdict `CI_BENCH=25 ./ci.sh` gates on
- **Error analysis**: Debug and analyze PAD errors
- **Regression testing**: Run automated tests
- **Reference generation**: Build documentation

## Access

Via web browser: `http://server/develop/`
