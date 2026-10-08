# Regression: the health check

## Introduction

Regression test for `$padHealth` (pad/lib/health.php): `?up` answers JSON - 200 and
`"status":"ok"` when every check passes, 503 and `"fail"` when one does. The checks are
DATA/ being writable, the database answering (an SQLite database in memory) and the
application's own `_health.php`. The index fetches it passing, with the application's check
failing (`&behind`) and with a database that is not there (`&nodb`), where the check says
`fail` and nothing of the reason or the credentials.

## Files

| File | Description |
|------|-------------|
| `index.php/pad` | Fetches `?up` three ways and shows status, type and body |
| `_health.php` | The application's own checks |
| `about.pad` | A page beside the health check |
| `.env` | SQLite in memory |
| `_config/config.php` | `_common` off, `$padHealth` on; `?nodb` a MySQL server that is not there |
