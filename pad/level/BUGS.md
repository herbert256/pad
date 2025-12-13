# Bugs Found in /home/herbert/pad/pad/level/

## 1. setup.php - Line 6: Typo in error message

**File:** `/home/herbert/pad/pad/level/setup.php`
**Line:** 6
**Bug Type:** Typo in error message

**Code:**
```php
return padError ( "To many Levels" );
```

**Issue:** "To" should be "Too" - incorrect grammar in error message.

**Correct:**
```php
return padError ( "Too many Levels" );
```

## 2. var.php - Line 5: Incorrect strpos check

**File:** `/home/herbert/pad/pad/level/var.php`
**Line:** 5
**Bug Type:** Logic error - incorrect boolean check for strpos

**Code:**
```php
$padPipe = strpos ( $padBetween, '|' );

if ( $padPipe ) {
```

**Issue:** `strpos()` returns `0` when the character is at position 0, which is falsy in PHP. This means if the pipe character `|` is at the beginning of `$padBetween`, the condition will fail when it should succeed.

**Correct:**
```php
if ( $padPipe !== FALSE ) {
```

**Impact:** If a pipe character appears at the start of the string, it will not be detected correctly, leading to incorrect parsing behavior.
