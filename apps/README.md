# PAD Applications

This directory contains PAD applications and examples.

## Applications

| Directory | Type | Description |
|-----------|------|-------------|
| [_common](_common/README.md) | Shared | Shared resources and utilities for all applications |
| [apps](apps/README.md) | Standard | Lists all PAD applications with descriptions from README files |
| [classicModels](classicModels/README.md) | Standard | PAD Select over the Classic Models sample database |
| [cli](cli/README.md) | CLI | Command-line interface for running PAD from terminal |
| [demo](demo/README.md) | Standard | Interactive demo with guestbook, todo, contact, counter, clock |
| [develop](develop/README.md) | Standard | Development tools and utilities for PAD |
| [hello](hello/README.md) | Minimal | Hello World example demonstrating page pairing |
| [manual](manual/README.md) | Standard | Interactive documentation and examples |
| [nono](nono/README.md) | Plain PHP | PHP application without PAD templating |
| [pad](pad/README.md) | Standard | PAD framework introduction and reference |
| [react](react/README.md) | Standard | PAD + React integration examples |
| [reference](reference/README.md) | Standard | Cross-reference and directory utilities |
| [regression](regression/README.md) | Standard | Automated regression testing for PAD - the sandbox cases, the crawl, and the runner for both suites |
| [regression2](regression2/README.md) | Test | The pages suite: every test is a real page, fetched over HTTP and compared with the answer beside it |
| [regression_cache_apcu](regression_cache_apcu/README.md) | Test | Regression test for the 'apcu' page cache - the index turns NO when the backend stops caching |
| [regression_cache_db](regression_cache_db/README.md) | Test | Regression test for the 'db' page cache - the index turns NO when the backend stops caching |
| [regression_cache_file](regression_cache_file/README.md) | Test | Regression test for the 'file' page cache - the index turns NO when the backend stops caching |
| [regression_cache_memcached](regression_cache_memcached/README.md) | Test | Regression test for the 'memcached' page cache - the index turns NO when the backend stops caching |
| [regression_cache_redis](regression_cache_redis/README.md) | Test | Regression test for the 'redis' page cache - the index turns NO when the backend stops caching |
| [regression_error_boot](regression_error_boot/README.md) | Test | Regression test for the 'boot' error action - the index turns NO when the action stops behaving |
| [regression_error_dump](regression_error_dump/README.md) | Test | Regression test for the 'dump' error action - the index turns NO when the action stops behaving |
| [regression_error_exit](regression_error_exit/README.md) | Test | Regression test for the 'exit' error action - the index turns NO when the action stops behaving |
| [regression_error_ignore](regression_error_ignore/README.md) | Test | Regression test for the 'ignore' error action - the index turns NO when the action stops behaving |
| [regression_error_log](regression_error_log/README.md) | Test | Regression test for the 'log' error action - the index turns NO when the action stops behaving |
| [regression_error_pad](regression_error_pad/README.md) | Test | Regression test for the 'pad' error action - the index turns NO when the action stops behaving |
| [regression_error_php](regression_error_php/README.md) | Test | Regression test for the 'php' error action - the index turns NO when the action stops behaving |
| [regression_error_stop](regression_error_stop/README.md) | Test | Regression test for the 'stop' error action - the index turns NO when the action stops behaving |
| [regression3](regression3/README.md) | Test | The pages of the suite that use _common - {example}, {demo}, {table} - fetched and compared the same way |
| [sequence](sequence/README.md) | Standard | Mathematical sequence subsystem demos |
| [structure](structure/README.md) | Example | Demonstrates PAD directory structure and nested `_xxx` directories |
| [test](test/README.md) | Minimal | A scratch application for trying things out, with `_common` switched off |

## Application Types

| Type | Description |
|------|-------------|
| Standard | Full PAD application with templates (.pad files) |
| Example | Demonstrates specific PAD features or patterns |
| Test | Test suite for validating PAD functionality |
| CLI | Command-line interface application |
| Shared | Resources shared across multiple applications |
| Plain PHP | PHP application that does not use PAD templating |

## Creating Applications

See [../docs/APP.md](../docs/APP.md) for complete instructions on creating and developing PAD applications.

## Documentation

For PAD framework documentation, see [../README.md](../README.md).
