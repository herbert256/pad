# Regression: application event hooks

## Introduction

Regression test for the `_events/` directory: an application's own hooks for four moments of
a request - an error, a statement of `db()`, a remote fetch, the finished page. Each page sets
one of them off and shows what its hook heard; the Regression suite compares every page with
its answer in `regression/regression/events/`.

The application runs under the `'ignore'` error action, so a page goes on after an error and
can show what the error hook kept.

## Files

| File | Description |
|------|-------------|
| `_events/error.php` | Keeps each error message in `$heardErrors` |
| `_events/sql.php` | Keeps each statement, its row count and whether it was timed |
| `_events/curl.php` | Keeps each fetch's result code and whether it failed |
| `_events/output.php` | Replaces a marker in the finished page |
| `sub/_events/output.php` | The subdirectory's own output hook, which wins for its pages |
| `error.php/pad` | Reads an undefined variable and shows what the hook heard |
| `sql.php/pad` | Runs one `db()` statement |
| `curl.php/pad` | Fetches a page of this application and a port nothing listens on |
| `output.pad`, `sub/output.pad` | Carry the marker the output hooks replace |
| `outputdata.php/pad`, `outputjson.php/pad` | A page exposing the marker as data, and the page that asks it for JSON: the hook sees the JSON |
| `_config/config.php` | The `'ignore'` action, the demo database, `_common` off |
