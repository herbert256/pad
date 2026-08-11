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
| [develop](develop/README.md) | Standard | Development tools for PAD - the source trimmer, the harvest of the reference and the examples, the error listing |
| [examples](examples/README.md) | Standard | Search the harvested examples and view one with its sources beside the rendered result |
| [hello](hello/README.md) | Minimal | Hello World example demonstrating page pairing |
| [manual](manual/README.md) | Standard | Interactive documentation and examples |
| [nono](nono/README.md) | Plain PHP | PHP application without PAD templating |
| [pad](pad/README.md) | Standard | PAD framework introduction and reference |
| [react](react/README.md) | Standard | PAD + React integration examples |
| [reference](reference/README.md) | Standard | Cross-reference and directory utilities |
| [regression/main](regression/main/README.md) | Standard | Automated regression testing for PAD - the runner for the eight suites and the fresh build |
| [regression/pages](regression/pages/README.md) | Test | The pages suite: every test is a real page, fetched over HTTP and compared with the answer beside it |
| [regression/framework](regression/framework/README.md) | Test | The Framework suite: the engine cases as pages, one fetched per case |
| [regression/regression](regression/regression/README.md) | Test | The Regression suite's prediction store - one answer per page of the self-testing applications and the runner |
| [regression/sequence](regression/sequence/README.md) | Test | The Sequence suite's prediction store - one answer per page of the sequence application |
| [regression/manual](regression/manual/README.md) | Test | The Manual suite's prediction store - one answer per page of the manual application |
| [regression/other](regression/other/README.md) | Test | The Other suite's prediction store - one answer per page of every application without a suite of its own |
| [regression/cache_apcu](regression/cache_apcu/README.md) | Test | Regression test for the 'apcu' page cache - the index turns NO when the backend stops caching |
| [regression/cache_db](regression/cache_db/README.md) | Test | Regression test for the 'db' page cache - the index turns NO when the backend stops caching |
| [regression/cache_file](regression/cache_file/README.md) | Test | Regression test for the 'file' page cache - the index turns NO when the backend stops caching |
| [regression/cache_memcached](regression/cache_memcached/README.md) | Test | Regression test for the 'memcached' page cache - the index turns NO when the backend stops caching |
| [regression/cache_redis](regression/cache_redis/README.md) | Test | Regression test for the 'redis' page cache - the index turns NO when the backend stops caching |
| [regression/config_typo](regression/config_typo/README.md) | Test | Regression test for the configuration word check - a typo'd output type answers with its name |
| [regression/error_boot](regression/error_boot/README.md) | Test | Regression test for the 'boot' error action - the index turns NO when the action stops behaving |
| [regression/error_dump](regression/error_dump/README.md) | Test | Regression test for the 'dump' error action - the index turns NO when the action stops behaving |
| [regression/error_exit](regression/error_exit/README.md) | Test | Regression test for the 'exit' error action - the index turns NO when the action stops behaving |
| [regression/error_ignore](regression/error_ignore/README.md) | Test | Regression test for the 'ignore' error action - the index turns NO when the action stops behaving |
| [regression/error_log](regression/error_log/README.md) | Test | Regression test for the 'log' error action - the index turns NO when the action stops behaving |
| [regression/error_pad](regression/error_pad/README.md) | Test | Regression test for the 'pad' error action - the index turns NO when the action stops behaving |
| [regression/error_php](regression/error_php/README.md) | Test | Regression test for the 'php' error action - the index turns NO when the action stops behaving |
| [regression/error_stop](regression/error_stop/README.md) | Test | Regression test for the 'stop' error action - the index turns NO when the action stops behaving |
| [regression/info](regression/info/README.md) | Test | Regression test for the five info modes with every option on - a NO line per mode that stops recording |
| [regression/output_console](regression/output_console/README.md) | Test | Regression test for the 'console' output type - the index turns NO when the writer stops behaving |
| [regression/output_download](regression/output_download/README.md) | Test | Regression test for the 'download' output type - the index turns NO when the writer stops behaving |
| [regression/output_file](regression/output_file/README.md) | Test | Regression test for the 'file' output type - the index turns NO when the writer stops behaving |
| [regression/output_web](regression/output_web/README.md) | Test | Regression test for the 'web' output type - the index turns NO when the writer stops behaving |
| [regression/try_log](regression/try_log/README.md) | Test | Regression test for the try guards under the 'log' action - caught, logged, and the page renders clean |
| [regression/try_pad](regression/try_pad/README.md) | Test | Regression test for the try guards under the 'pad' action - caught and reported into the page |
| [regression/errors](regression/errors/README.md) | Test | The Errors suite: the tests that fail on purpose, answered lean under the boot action - no dumps |
| [regression/common](regression/common/README.md) | Test | The pages of the suite that use _common - {example}, {demo}, {table} - fetched and compared the same way |
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
