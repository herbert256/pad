# Bugs Found in /home/herbert/pad/pad/lib/

## 1. api.php - Line 18: Undefined variable $padScript

**File:** `/home/herbert/pad/pad/lib/api.php`
**Line:** 18
**Bug Type:** Undefined variable

**Code:**
```php
function padFastLink ( $padPage, $vars ) {

  global $padFastLink, $padSesID, $padReqID, $padHost;

  $vars ['padPage']  = $padPage;
  $vars ['padSesID'] = $padSesID;
  $vars ['padRefID'] = $padReqID;

  $fast = padRandomString ($padFastLink);

  padDb (
    "insert into links values('{0}','{1}')",
    [$fast, serialize($vars)]
  );

  return "$padHost$padScript?$fast";

}
```

**Issue:** The variable `$padScript` is used on line 18 but is not declared in the `global` statement on line 5. This will cause an undefined variable error or return an empty value.

**Impact:** The function will not return the correct URL. The script path component will be missing from the returned link.

**Fix:** Add `$padScript` to the global variables:
```php
global $padFastLink, $padSesID, $padReqID, $padHost, $padScript;
```

## 2. level.php - Line 121: Extra semicolon

**File:** `/home/herbert/pad/pad/lib/level.php`
**Line:** 121
**Bug Type:** Syntax error - double semicolon

**Code:**
```php
$padEnd [$pad] = strpos ( $padOut [$pad], '}' );;
```

**Issue:** There are two semicolons at the end of the line instead of one.

**Fix:** Remove the extra semicolon:
```php
$padEnd [$pad] = strpos ( $padOut [$pad], '}' );
```

**Impact:** While PHP tolerates this (treats it as an empty statement), it's a syntax error that could cause issues with some parsers or code analysis tools.
