# Bugs Found in /home/herbert/pad/pad/start/

## 1. Missing $ Variable Prefix in /home/herbert/pad/pad/start/end/dat.php

**Location:** Line 5
**File:** /home/herbert/pad/pad/start/end/dat.php

**Bug:**
```php
foreach ( padStrDat as $padStrVal )
```

**Issue:** Missing `$` prefix before `padStrDat`. This will cause a PHP fatal error as `padStrDat` will be interpreted as a constant rather than a variable.

**Expected:**
```php
foreach ( $padStrDat as $padStrVal )
```

---

## 2. Missing $ Variable Prefix in /home/herbert/pad/pad/start/start/dat.php

**Location:** Line 6
**File:** /home/herbert/pad/pad/start/start/dat.php

**Bug:**
```php
foreach ( padStrDat as $padStrVal )
```

**Issue:** Missing `$` prefix before `padStrDat`. This will cause a PHP fatal error as `padStrDat` will be interpreted as a constant rather than a variable.

**Expected:**
```php
foreach ( $padStrDat as $padStrVal )
```

---

## 3. Missing $ Variable Prefixes in /home/herbert/pad/pad/start/start/resetPad.php

**Location:** Lines 4 and 7
**File:** /home/herbert/pad/pad/start/start/resetPad.php

**Bug on Line 4:**
```php
foreach ( padStrDat as $padStrVal )
```

**Issue:** Missing `$` prefix before `padStrDat`.

**Expected:**
```php
foreach ( $padStrDat as $padStrVal )
```

**Bug on Line 7:**
```php
foreach ( padStrSto as $padStrVal )
```

**Issue:** Missing `$` prefix before `padStrSto`.

**Expected:**
```php
foreach ( $padStrSto as $padStrVal )
```

---

## 4. Missing $ Variable Prefix in /home/herbert/pad/pad/start/start/stores.php

**Location:** Line 7
**File:** /home/herbert/pad/pad/start/start/stores.php

**Bug:**
```php
foreach ( padStrSto as $padStrVal )
```

**Issue:** Missing `$` prefix before `padStrSto`. This will cause a PHP fatal error as `padStrSto` will be interpreted as a constant rather than a variable.

**Expected:**
```php
foreach ( $padStrSto as $padStrVal )
```
