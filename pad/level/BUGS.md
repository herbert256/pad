# Bug Report: /home/herbert/pad/pad/level/

## Bug 1: Array Index Not Checked
**File:** `/home/herbert/pad/pad/level/close.php`
**Line:** 3, 8
**Severity:** Medium

**Description:**
The code accesses array indices without checking if they exist first, which could cause PHP notices/warnings.

```php
if ( ! str_contains( $padOpt [$pad] [0], '}' ) )
  return;

$padClosePad = padCode ( $padOpt [$pad] [0] );
```

**Issue:** `$padOpt[$pad][0]` may not exist, causing undefined index errors.

---

## Bug 2: Potential Infinite Loop in split.php
**File:** `/home/herbert/pad/pad/level/split.php`
**Line:** 10-23
**Severity:** High

**Description:**
The while loop looking for `{else}` could potentially run indefinitely if the data structure is corrupted.

```php
$padPos = strpos ( $padBase [$pad], '{else}');

while ( $padPos !== FALSE) {
  if  ( padOpenCloseCount ( substr ( $padBase [$pad], 0, $padPos ), $padOpenClose) ) {
    // ...
    return;
  }
  $padPos = strpos ( $padBase [$pad], '{else}', $padPos+1);
}
```

**Issue:** If `padOpenCloseCount()` never returns TRUE and `{else}` appears multiple times, this could loop many times.

---

## Bug 3: Array Index Access Without Validation
**File:** `/home/herbert/pad/pad/level/parms/parms.php`
**Line:** 37-38
**Severity:** Medium

**Description:**
Array index `[1]` is set without checking if it already exists.

```php
if ( ! isset ( $padOpt [$pad] [1] ) )
  $padOpt [$pad] [1] = '';
```

**Issue:** While this check exists, the pattern suggests other code may access `$padOpt[$pad][1]` without validation.

---

## Bug 4: Deprecated List Syntax
**File:** `/home/herbert/pad/pad/level/start_end/end1.php`
**Line:** 6
**Severity:** Low

**Description:**
Using `list()` with `explode()` without checking the array size could cause notices if the delimiter is not found.

```php
list ( $padBase [$pad], $padEndBase [$pad] ) = explode ( '@end@', $padBase[$pad], 2 );
```

**Issue:** If `@end@` is not found, only one element is returned, causing undefined index for the second list variable.

---

## Bug 5: Same Issue with list() and explode()
**File:** `/home/herbert/pad/pad/level/start_end/start1.php`
**Line:** 6
**Severity:** Low

**Description:**
Similar to Bug 4, using `list()` with `explode()` without validation.

```php
list ( $padBase [$pad], $padStartBase [$pad] ) = explode ( '@start@', $padBase [$pad], 2 );
```

**Issue:** If `@start@` is not found in the string, `explode()` will only return one element, leaving the second list variable undefined.

---

## Bug 6: Potential Division by Zero or Logic Error
**File:** `/home/herbert/pad/pad/level/pair.php`
**Line:** 17
**Severity:** Medium

**Description:**
The code checks if opening and closing braces count match, but doesn't handle the case where they're both zero.

```php
} while ( substr_count($padBetweenCheck, '{') <> substr_count($padBetweenCheck, '}') );
```

**Issue:** Deprecated `<>` operator and potential logic issues with brace counting.

---

## Bug 7: Undefined Variable Access
**File:** `/home/herbert/pad/pad/level/tag.php`
**Line:** 14-17
**Severity:** Medium

**Description:**
Variables `$padTypeSeq` and `$padTypeGiven` may not be defined in all code paths.

```php
if ( $padTypeSeq )
  $padPairTag = $padTypeSeq . ':' . $padTypeCheck;
else
  $padPairTag = ( $padTypeGiven ) ? $padTypeResult . ':' . $padTypeCheck : $padTypeCheck;
```

**Issue:** `$padTypeSeq` and `$padTypeGiven` might not be initialized.

---

## Bug 8: Inconsistent Error Message
**File:** `/home/herbert/pad/pad/level/setup.php`
**Line:** 5-6
**Severity:** Low

**Description:**
Typo in error message.

```php
if ( $pad > 25 )
  return padError ( "To many Levels" );
```

**Issue:** "To many" should be "Too many".

---

## Bug 9: Potential Undefined Index
**File:** `/home/herbert/pad/pad/level/var.php`
**Line:** 5-6
**Severity:** Medium

**Description:**
The code uses `strpos()` which returns `FALSE` if not found, but then uses it in string operations without checking.

```php
$padPipe = strpos ( $padBetween, '|' );

if ( $padPipe ) {
  $padFld  = rtrim(substr($padBetween, 1, $padPipe-1));
```

**Issue:** `strpos()` returns `0` when the pipe is at position 0, which evaluates to `false` in the if condition, causing incorrect behavior.

---

## Bug 10: Array Access Without Bounds Check
**File:** `/home/herbert/pad/pad/level/setup.php`
**Line:** 68
**Severity:** Low

**Description:**
Setting array without validation that index exists.

```php
$padStartBase  [$pad] = [];
```

**Issue:** While initializing is generally safe, the pattern shows potential for issues in related code.
