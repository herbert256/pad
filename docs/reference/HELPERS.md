# PHP Helpers Reference

PAD pages do their work in the page's `.php` file, and the template renders what it leaves
behind. The functions on this page are the helpers that PHP code can use - the kind modern
PHP frameworks ship (Laravel's `Arr`, `Str`, `blank()`, `session()`, `Cache::remember()`,
`now()`, `encrypt()` ...), written the PAD way: plain functions, a `pad` prefix, no classes,
no Composer. They are loaded on every request, like every file in `pad/lib/`.

```php
<?php                                         // orders.php

  $orders  = db ( "ARRAY * FROM orders" );
  $byState = padArrGroupBy ( $orders, 'status' );
  $total   = padArrSum ( $orders, 'amount' );
  $title   = padStrHeadline ( padRequest ( 'view', 'all_orders' ) );

?>
```

| Group | Functions | Manual page |
|-------|-----------|-------------|
| [Arrays](#arrays) | `padArrGet`, `padArrSet`, `padArrPluck`, `padArrGroupBy`, ... | arrays |
| [Strings](#strings) | `padStrSlug`, `padStrLimit`, `padStrCamel`, `padStrUuid`, ... | strings |
| [Values](#values) | `padBlank`, `padFilled`, `padValue`, `padRetry`, `padOnce`, ... | values |
| [Numbers](#numbers) | `padNumberAbbreviate`, `padNumberOrdinal`, ... | numbers |
| [Requests and sessions](#requests-and-sessions) | `padRequest`, `padSession`, `padOld`, `padUrl`, `padBack`, `padAbort`, ... | requests_and_sessions |
| [Environment and cache](#environment-and-cache) | `padEnv`, `padRemember`, `padCacheGet`, `padRateLimit`, ... | environment_and_cache |
| [Dates and logging](#dates-and-logging) | `padNow`, `padAgo`, `padLog`, ... | dates_and_logging |
| [Hashing and encryption](#hashing-and-encryption) | `padHash`, `padEncrypt`, `padDecrypt`, ... | hashing_and_encryption |

---

## Arrays

<!-- helpers: arrays -->


---

## Strings

<!-- helpers: strings -->


---

## Values

`pad/lib/helpers.php` - weighing, defaulting and guarding values; manual page *Value helpers*.

| Function | Answers |
|----------|---------|
| `padBlank ( $value )` | TRUE for NULL, `''` and whitespace-only text (unicode spaces too), an empty array, an empty Countable or Stringable; FALSE for `0`, `'0'`, `0.0`, FALSE, TRUE and every other value |
| `padFilled ( $value )` | `! padBlank ( $value )` |
| `padValue ( $value, ...$args )` | a Closure called with the arguments; anything else as it is |
| `padTransform ( $value, $callback, $default = NULL )` | `$callback ( $value )` when the value is filled, else the default - a Closure default called with the blank value |
| `padTap ( $value, $callback )` | calls `$callback ( $value )`, answers the value |
| `padRetry ( $times, $callback, $sleepMilliseconds = 0, $when = NULL )` | the callback's answer, called with the attempt number (1, 2 ...) until it does not throw, at most `$times` times |
| `padRescue ( $callback, $rescue = NULL, $report = TRUE )` | the callback's answer, or `padValue ( $rescue, $e )` when it throws |
| `padOnce ( $callback )` | the callback's answer, run once per request for the file and line that call padOnce |

```php
<?php                                           // checkout.php

  if ( padBlank ( $coupon ) )                   // '0' is a coupon, '  ' is none
    $coupon = 'NONE';

  $title = padTransform ( $customer ['name'], fn ( $n ) => ucwords ( $n ), 'Guest' );

  $rates = padRetry ( 3, fn ( $attempt ) => fetchRates (), [ 100, 500 ] );   // throws when down

  $news  = padRescue ( fn () => newsFeed (), [], FALSE );   // the page goes on without it

  function settings () {
    return padOnce ( fn () => db ( "RECORD * FROM settings" ) );   // one query per request
  }

?>
```

- `padRetry` waits `$sleepMilliseconds` between attempts: one number for every wait, or a list
  with a wait per attempt whose last one repeats when there are more attempts. `$when`, given
  the Throwable, decides whether another attempt is worth it; when it answers falsy, or after
  the last attempt, the Throwable is thrown on.
- `padRescue` catches every Throwable - an Exception and an Error (`intdiv ( 1, 0 )`) alike.
  With `$report` the failure is written to PHP's error log: `padRescue: RuntimeException:
  message in file:line`. A PAD error inside the callback is not an exception (it ends the
  request as always) - except under `$padErrorAction = 'php'`, which throws.
- `padOnce` keeps its results for the request only. A callback that throws gave no result, so
  the next call from that line runs it again; two padOnce calls on one line share their result.
- Only a Closure is called by `padValue`, `padTransform`'s default and `padRescue`'s rescue
  value: a string such as `'date'` is a value there. The callbacks of `padTransform`, `padTap`,
  `padRetry`, `padRescue` and `padOnce` may be any callable - a Closure, `'ucfirst'`, `[ $object,
  'method' ]`.
- Wrong input is reported with `padError`, naming the function: a callback that is not callable
  (`padTransform: the callback 'ucfirts' is not a function or a Closure`), a number of attempts
  below 1, a wait that is not a number of 0 or more. The function then answers NULL (`padTap`
  the value).


---

## Numbers

<!-- helpers: numbers -->


---

## Requests and sessions

<!-- helpers: requests and sessions -->


---

## Environment and cache

| Function | What it answers |
|----------|-----------------|
| `padEnv ( $key, $default = NULL )` | The value of an environment key: the real environment (`getenv`, `$_ENV`, `$_SERVER`) first, then the application's `_config/.env`, then `.env` in the PAD home, else the default - a Closure default is called |

`pad/lib/env.php`. Machine-specific values and secrets - a database password, an API key, a
debug switch - stay out of the code and out of git. The engine loads `pad/lib/` before it
reads any configuration, so a configuration file can use it:

```php
<?php                                         // _config/config.php

  $padSqlPassword = padEnv ( 'DB_PASSWORD' );
  $padSqlUser     = padEnv ( 'DB_USER', 'shop' );
  $padToolbar     = padEnv ( 'APP_DEBUG', FALSE ) ? 'local' : FALSE;

?>
```

The `.env` format - the one other tools read too:

```
# a comment
APP_NAME=Shop                   a bare value, trimmed; a # at its start or after a space starts a comment
export DB_HOST=localhost        export in front, as a shell script writes it
GREETING="Hello\nWorld"         double quotes: \n \r \t \" \\ \$ and ${OTHER}
PATTERN='${not} \n expanded'    single quotes: the text exactly as written
DEBUG=false                     true, false, null, empty - also (true) ..., any case - are TRUE, FALSE, NULL, ''
NOTE="two
lines"                          a quoted value may run over several lines
```

Edge rules:

- The key must be a non-empty string; anything else is a `padError` naming `padEnv`, and the
  default is answered.
- A missing key answers the default. A key whose value is `null` answers NULL, not the
  default - the key is there and says so. `empty` and an empty value answer `''`.
- A quoted value is always text: `"false"` is the word, not FALSE. The words apply to bare
  values and to values from the real environment.
- `${OTHER}` is OTHER from the real environment, else as set earlier in the same file, else
  `''`; in double quotes and in bare values, never in single quotes. `\$` writes a dollar.
- A key written twice: the later line wins. A line that is not `KEY=VALUE`, a quoted value
  that never closes, or text after a closing quote is an error naming the file and the line
  number - never the line, which may hold the secret. While a configuration file is being
  read, before PAD's error handling stands, the fault is thrown to the boot handlers.
- A key starting with `HTTP_` is never read from the real environment: those are the request's
  headers, sent by the client (a `Proxy:` header must not become `HTTP_PROXY`).
- Each file is read once per request, kept in a static rather than a global, so its secrets
  are not among the variables a dump lists.
- No URL reaches `apps/` - the web server serves `www/` only - so an application's
  `_config/.env` is never served. The repository's `.gitignore` keeps `/.env`, the PAD home's
  file, out of git.


---

## Dates and logging

<!-- helpers: dates and logging -->


---

## Hashing and encryption

<!-- helpers: hashing and encryption -->
