# Regression: environment values and the application cache

## Introduction

Regression test for `padEnv` where it is needed first: in a configuration file. This
application's `_config/config.php` reads `$padSqlPassword` with `padEnv` from the `.env` file
beside it - which works because the engine loads its library before it reads any
configuration. The index page shows that password and every key of the fixture, read with
`padEnv`: bare, exported, double- and single-quoted values, escapes, `${OTHER}` references, a
value over two lines, the words true, false, null and empty, a key set twice, and two keys the
real environment answers instead of the file - except a request header, which is never the
environment.

The application cache is here too, for what needs an application of its own: the flush
removes every entry of the application - which in a shared application would take the
entries of the tests running beside it - and the burst page sends ten requests at once that
each count a hit on one rate limit, so the lock that makes them take turns is seen to work:
without it, hits are lost. The Regression suite fetches these pages one at a time and compares
each with its answer in `regression/regression/env/`.

## Files

| File | Description |
|------|-------------|
| `_config/.env` | The fixture - every form a line of a `.env` file takes; no real secret |
| `_config/config.php` | Reads `$padSqlPassword` with `padEnv`, `_common` off |
| `index.php/pad` | Every key of the fixture, read with `padEnv`, and the password the configuration read |
| `cache.php/pad` | Two entries and a rate limit, before and after `padCacheFlush` |
| `burst.php/pad` | Ten requests at once to `hit`, and how many hits the rate limit counted |
| `hit.php/pad` | One hit on the rate limit `burst` counts |
