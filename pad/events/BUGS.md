# Bug Report - /home/herbert/pad/pad/events/

## Bug 1: Redundant Condition Check
**File:** `/home/herbert/pad/pad/events/data.php`
**Line:** 8
**Severity:** Low (Code Quality)

### Issue
The condition `$padInfoTrace` is checked twice unnecessarily on line 8.

### Current Code
```php
if ( ! $GLOBALS ['padInfoTrace'] )
    return;

if ( $padInfoTraceDataLvl ) {

    if ( ! $padInfoTrace or ! $padInfoTraceDefault and padIsDefaultData ( $padData [$pad] ) )
      return;

   if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'level', 'data', $padData [$pad] );
   //  ^^^ This check is redundant - we already checked at the top
```

### Fix Needed
Remove the redundant check on line 11:
```php
padInfoTrace ( 'level', 'data', $padData [$pad] );
```
The outer check on line 3 already ensures `$GLOBALS ['padInfoTrace']` is true.

---

## Bug 2: Redundant Condition Check
**File:** `/home/herbert/pad/pad/events/sql.php`
**Line:** 6
**Severity:** Low (Code Quality)

### Issue
Line 3 checks if `$GLOBALS ['padInfoTrace']` is false and returns. Line 6 checks it again - this is redundant.

### Current Code
```php
if ( ! $GLOBALS ['padInfoTrace'] )
    return;

if ( ! $GLOBALS ['padInfoTrace'] or ! $GLOBALS ['padInfoTraceSql'] )
    //  ^^^ This part is redundant
    return;
```

### Fix Needed
Simplify line 6:
```php
if ( ! $GLOBALS ['padInfoTraceSql'] )
    return;
```

---

## Bug 3: Missing Space Concatenation
**File:** `/home/herbert/pad/pad/events/eval/error.php`
**Line:** 8
**Severity:** Medium

### Issue
Missing space or separator between file path and line number/message concatenation.

### Current Code
```php
$error = $e->getFile() . ':' .  $e->getLine() . $e->getMessage();
//                                           ^^^ Missing space/separator
```

### Expected Output
```
/path/to/file.php:123Some error message
```

### Should Be
```
/path/to/file.php:123 Some error message
```

### Fix Needed
```php
$error = $e->getFile() . ':' .  $e->getLine() . ' ' . $e->getMessage();
```

---

All other files have been analyzed and no additional bugs were found.
