# Bugs Found in /home/herbert/pad/pad/sequence/

## 1. Misspelled Filename

**File:** `/home/herbert/pad/pad/sequence/options/types/ramdomly.php`
**Bug Type:** Typo in filename

**Issue:** The filename is spelled "ramdomly.php" but should be "randomly.php" (missing the 'n'). This typo could cause file-not-found errors if code tries to include the correctly-spelled filename.

---

## 2. Division by Zero Risk in average.php

**File:** `/home/herbert/pad/pad/sequence/actions/types/average.php`
**Line:** 3
**Bug Type:** Potential division by zero

**Code:**
```php
$pqResult = [ $pqActionKey => array_sum ( $pqResult ) / count ( $pqResult ) ];
```

**Issue:** If `$pqResult` is an empty array, `count($pqResult)` will be 0, causing a division by zero error. There should be a check to ensure the array is not empty before performing the division.

---

## 3. Division by Zero Risk in divide/loop.php

**File:** `/home/herbert/pad/pad/sequence/types/divide/loop.php`
**Line:** 3
**Bug Type:** Potential division by zero

**Code:**
```php
return $pqLoop / $pqParm;
```

**Issue:** If `$pqParm` is 0, this will cause a division by zero error. There should be a check to ensure `$pqParm` is not zero before performing the division.

---

## 4. Typo in subtract Directory Name

**File:** `/home/herbert/pad/pad/sequence/types/subtract/`
**Bug Type:** Typo in directory name

**Issue:** The directory is spelled "subtract" but should be "subtract" (missing the second 't'). This is a common misspelling and could cause confusion or file-not-found errors if code tries to reference the correctly-spelled directory name.
