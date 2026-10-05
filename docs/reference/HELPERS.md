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

<!-- helpers: environment and cache -->


---

## Dates and logging

<!-- helpers: dates and logging -->


---

## Hashing and encryption

<!-- helpers: hashing and encryption -->
