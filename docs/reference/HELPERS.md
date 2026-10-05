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

`pad/lib/arr.php` - reading and writing nested arrays by a dot path, and the everyday work
on a list of rows: one field of every row, the rows that pass, grouped, keyed, sorted,
summed. Manual page: *Array helpers* (`?pages/arrays`).

| Function | What it answers |
|----------|-----------------|
| `padArrGet ( $target, $key, $default = NULL )` | The value at a dot path, or the default (a Closure default is called); a NULL key answers the target itself |
| `padArrSet ( &$array, $key, $value )` | Sets the value at a dot path, making every missing or plain level an array; answers the array. A `*` segment sets in every item; a NULL key replaces the whole array |
| `padArrHas ( $array, $keys )` | TRUE when every dot path given exists - a NULL value exists. No keys is FALSE |
| `padArrForget ( &$array, $keys )` | Removes one or several dot paths; answers the array. A `*` segment removes from every item (as the last segment: every item) |
| `padArrOnly ( $array, $keys )` | The top-level keys given, in the order given; a key not there is left out |
| `padArrExcept ( $array, $keys )` | The array without the keys given - dot paths too; the array given (and an object in it) is not changed |
| `padArrPluck ( $rows, $value, $key = NULL )` | One field of every row as a list, or keyed by another field; both dot paths (or callbacks) |
| `padArrWhere ( $rows, $key, $operator = NULL, $value = NULL )` | The rows that pass, keys kept - see the forms below |
| `padArrFirst ( $array, $callback = NULL, $default = NULL )` | The first value, or the first the callback (value, key) is true for, or the default |
| `padArrLast ( $array, $callback = NULL, $default = NULL )` | The same from the end |
| `padArrGroupBy ( $rows, $key )` | `[ group => [ rows ] ]` by a dot path or a callback, groups in the order of their first row, each group a list |
| `padArrKeyBy ( $rows, $key )` | The rows keyed by a dot path or a callback; a later row wins |
| `padArrSortBy ( $rows, $key, $descending = FALSE )` | The rows sorted by a dot path, a callback, or (NULL key) the values; stable; string keys kept, a list numbered again. `$descending`: TRUE or `'desc'` |
| `padArrFlatten ( $array, $depth = INF )` | The values as one list, nested arrays opened `$depth` levels deep (0: none) |
| `padArrDot ( $array, $prepend = '' )` | A nested array as one level with dot path keys; an empty array stays a value |
| `padArrUndot ( $array )` | Dot path keys made nested arrays again |
| `padArrWrap ( $value )` | NULL `[]`, an array itself, anything else `[ $value ]` |
| `padArrSum ( $rows, $key = NULL )` | The sum of the numbers among the values, or a field of every row; 0 for none |
| `padArrAvg ( $rows, $key = NULL )` | Their average; NULL for none |
| `padArrMin ( $rows, $key = NULL )` | The smallest; NULL for none |
| `padArrMax ( $rows, $key = NULL )` | The largest; NULL for none |

**Dot paths.** `'customer.address.city'`; a segment may be an integer key (`'lines.0.sku'`).
A key that holds a dot itself - `'a.b'` as one key - is found as it is before the path is
split. A `*` segment maps over every item of that level and answers a list
(`'lines.*.sku'`); an item without the rest of the path gives NULL in the list, and a second
`*` makes one list of the lists (`'orders.*.lines.*.sku'`). Objects are read like arrays: an
`ArrayAccess` object through its offsets, any other object through its public properties.

```php
<?php

  $order = json_decode ( $answer, TRUE );                       // an API's order

  $city  = padArrGet ( $order, 'customer.address.city', 'unknown' );
  $skus  = padArrGet ( $order, 'lines.*.sku' );                   // [ 'TEA', 'MUG' ]
  padArrSet    ( $order, 'customer.address.zip', '2311 AB' );
  padArrForget ( $order, 'customer.password, lines.*.cost' );

  $rows     = db ( "ARRAY * FROM orders" );
  $paid     = padArrWhere   ( $rows, 'status', 'paid' );
  $large    = padArrWhere   ( $rows, 'total', '>=', 100 );
  $someC    = padArrWhere   ( $rows, 'customer', 'like', 'c%' );
  $choices  = padArrPluck   ( $customers, 'name', 'id' );         // id => name, a select box
  $byState  = padArrGroupBy ( $rows, 'status' );
  $newest   = padArrSortBy  ( $rows, 'created', 'desc' );
  $firstBig = padArrFirst   ( $rows, fn ( $row ) => $row ['total'] > 500 );
  $total    = padArrSum     ( $rows, 'total' );
  $public   = padArrExcept  ( $user, 'password, login.ip' );

?>
```

**padArrWhere forms.**

| Call | Keeps the rows where |
|------|----------------------|
| `padArrWhere ( $rows, fn ( $row, $key ) => ... )` | the callback answers true |
| `padArrWhere ( $rows, 'status', 'paid' )` | the field equals the value (loose `==`) |
| `padArrWhere ( $rows, 'total', '>', 100 )` | the field compares so with the value |
| `padArrWhere ( $rows, 'active' )` | the field is true in PHP's sense |

Operators: `= == === != <> !== < > <= >=`, PAD's `eq ne lt gt le ge`, `in` and `not in` with a
list of values, `like` with `%` (any run) and `_` (one character), case-insensitive and
unicode-aware, `\%` and `\_` the characters themselves. Case and spaces in the operator do
not matter (`'NOT IN'`). Equality is PHP's loose `==`, so the `'5'` a database answers equals
a `5` written in PHP; `===` is strict. A NULL field matches no `like`; an object compared with
a plain value is neither equal, smaller nor larger (no PHP warning).

**Edge rules.**

- A missing path answers the default; a path whose value is NULL answers NULL - it exists
  (`padArrHas` says TRUE).
- Where a set is expected - `$rows`, `$array` - an array, a `Traversable` or an object's
  public properties are the set; NULL, `''`, `0`, FALSE and any other plain value are an empty
  one: `padArrPluck ( NULL, 'name' )` is `[]`, `padArrFirst ( '', NULL, 'none' )` is `'none'`.
  A string is never indexed into.
- Keys (`$keys` of Has, Forget, Only, Except) are an array, a comma-separated string
  (`'id, name'` - trimmed), or one integer; NULL or `[]` is none.
- A field (`$key`, `$value`) is a dot path or a callback: a Closure, an invokable object or
  `[ $object, 'method' ]`. A string is always a dot path - a field called `date` never runs
  PHP's `date()`. A user callback gets the row and its key; one of PHP's own functions given
  as a callback (`is_numeric(...)`) gets the row only. `padArrFirst`/`padArrLast` take a
  callable string too (`'is_numeric'`).
- A key made from a row's value (Pluck, GroupBy, KeyBy): NULL is `''`, TRUE/FALSE 1/0, a
  whole float its integer, an enum its value or name, a `Stringable` its text.
- Sum, Avg, Min and Max count ints, floats and numeric strings (as numbers) only: NULL, `''`,
  `'n/a'`, TRUE and arrays are left out, not counted as 0.
- `padArrSet` on an object writes in place: an `ArrayAccess` offset, a `stdClass` property,
  a public property it has, or through `__set`.
- Reported with `padError` (naming the function), after which the function answers an empty
  value of its kind: a key that is no dot path (an array, TRUE, an object), a key list item
  that is no key, an unknown `padArrWhere` operator, `in` without a list, `like` without a
  text pattern, a row value that cannot be a key (an array), a `padArrFirst`/`padArrLast`
  callback that is not callable, a depth that is negative or no number, a `padArrSet` or
  `padArrForget` an object refuses (no such public property, a readonly one).


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

`pad/lib/number.php` - numbers written for people, and kept between bounds; manual page
*Number helpers*. The pipes `abbreviate` and `ordinal` are the template side
([FUNCTIONS.md](FUNCTIONS.md#helper-pipes)), and the `bytes` pipe answers what
`padNumberFileSize` answers.

| Function | Answers |
|----------|---------|
| `padNumberFormat ( $number, $decimals = 0, $locale = NULL )` | the number with exactly `$decimals` decimals, grouped, the locale's way - `$padLocale` unless one is given: `1,234,567.89` in en, `1.234.567,89` in nl |
| `padNumberPercentage ( $number, $precision = 0 )` | a share counted in hundreds with exactly `$precision` decimals, the request's locale's way: `25` → `25%`, `25.55` with 1 → `25.6%` (`25,6%` in nl) |
| `padNumberAbbreviate ( $number, $precision = 0 )` | `1000` → `1K`, `1500` → `2K`, with precision 1 `1.5K`; then `M`, `B`, `T`, `Q`; below 1000 no letter |
| `padNumberForHumans ( $number, $precision = 0 )` | `1 thousand`, `1.5 million`, `2 billion`, `trillion`, `quadrillion` |
| `padNumberFileSize ( $bytes, $precision = 0 )` | units of 1024: `1536` → `2 KB`, with precision 1 `1.5 KB`; `B` to `EB` |
| `padNumberOrdinal ( $number )` | `1st 2nd 3rd 4th 11th 12th 13th 21st 101st 111th` - English |
| `padNumberClamp ( $number, $min, $max )` | the number, or the bound it went past - a number |

```php
<?php                                           // stats.php

  $revenue   = padNumberFormat ( $sum, 2 );                 // 1,234,567.89
  $growth    = padNumberPercentage ( $rise * 100, 1 );      // 12.5%
  $followers = padNumberAbbreviate ( $count, 1 );           // 48.2K
  $headline  = 'Over ' . padNumberForHumans ( $visits ) . ' visits';   // Over 2 million visits
  $backup    = padNumberFileSize ( filesize ( $file ), 1 ); // 5.1 MB
  $place     = padNumberOrdinal ( $rank );                  // 22nd
  $page      = padNumberClamp ( $page, 1, $pages );         // never past the last page

?>
```

- `padNumberFormat` and `padNumberPercentage` write exactly the decimals asked for, rounding
  half up (`2.5` → `3`), and a value that rounds to zero is never `-0`. PHP's intl extension
  (`NumberFormatter`) writes them when it is there; without it they are written the plain
  `number_format` way (`1,234.50`, `25.6%`).
- `padNumberAbbreviate`, `padNumberForHumans` and `padNumberFileSize` write at most
  `$precision` decimals and drop the trailing zeros (`1000` with 1 is `1K`). The unit is chosen
  on the number as it will be written - `999999` is `1M`, `1048575` bytes `1 MB` - the sign is
  kept (`-1500` → `-2K`), and past the last unit the number grows (`5000Q`). They are English,
  with a point before the decimals, whatever the locale. `padNumberFileSize ( $n, 2 )` is
  exactly `{$n | bytes}`.
- Numeric text counts as the number it holds (`'1234'`, `' 12 '`). NULL, FALSE and empty text
  are nothing to write: the answer is `''` (`padNumberClamp`: NULL) and nothing is reported.
- Reported with `padError`, naming the function, and answered `''` (`padNumberClamp`: NULL):
  a value that is no number (`'twelve'`, an array, TRUE, INF), a precision or number of
  decimals that is negative or has a fraction (NULL is the default 0), a fraction handed to
  `padNumberOrdinal`, a locale intl does not know, and for `padNumberClamp` a missing bound or
  a minimum above the maximum.


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
