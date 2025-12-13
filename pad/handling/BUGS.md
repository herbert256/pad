# Bug Report - /home/herbert/pad/pad/handling/

## High Bugs

### 1. Undefined Function Call
**File:** `/home/herbert/pad/pad/handling/types/page.php`
**Line:** 9
**Description:** Function `padHandGo()` is called but not defined in this file or visible included files. Will cause fatal error.
**Severity:** High

### 2. Undefined Function Call
**File:** `/home/herbert/pad/pad/handling/types/random.php`
**Line:** 8
**Description:** Function `pqRandom()` is called but not defined. Will cause fatal error.
**Severity:** High

### 3. Undefined Function Call
**File:** `/home/herbert/pad/pad/handling/types/row.php`
**Line:** 5
**Description:** Function `padHandGo()` is called but not defined. Will cause fatal error.
**Severity:** High

### 4. Undefined Function Call
**File:** `/home/herbert/pad/pad/handling/types/start.php`
**Line:** 18
**Description:** Function `padHandGo()` is called but not defined. Will cause fatal error.
**Severity:** High

### 5. Undefined Function Call
**File:** `/home/herbert/pad/pad/handling/types/trim.php`
**Line:** 11, 12, 16, 19
**Description:** Function `pqTruncate()` is called but not defined. Will cause fatal error.
**Severity:** High

### 6. Undefined Function Call
**File:** `/home/herbert/pad/pad/handling/types/sort.php`
**Line:** 7, 23
**Description:** Function `padExplode()` is called but not defined. Will cause fatal error.
**Severity:** High

### 7. Use of extract() Function
**File:** `/home/herbert/pad/pad/handling/handling.php`
**Line:** 7
**Description:** `extract()` is used which imports variables from an array into the current symbol table. This is dangerous as it can overwrite existing variables and makes code harder to debug. It's considered a security risk and bad practice.
**Severity:** High

## Medium Bugs

### 8. Missing Array Key Validation
**File:** `/home/herbert/pad/pad/handling/types/dedup.php`
**Line:** 6
**Description:** Accesses `$padName[$pad]` without checking if the key exists. Will cause undefined index warning.
**Severity:** Medium

### 9. Missing Array Key Validation
**File:** `/home/herbert/pad/pad/handling/types/splice.php`
**Line:** 6, 7
**Description:** Uses null coalescing on array access but the outer array `$padExplode` might not have indices 0 or 1. Additional validation needed.
**Severity:** Medium

### 10. Undefined Variables Throughout
**Files:** All files in this directory
**Description:** Variables like `$padData`, `$pad`, `$padHandCnt`, `$padPrm`, `$padHandName`, etc. are used without being defined in local scope. They appear to come from calling context but have no validation.
**Severity:** Medium

### 11. Undefined Constants
**Files:** Multiple files
**Description:** Constant `PAD` is used throughout but not defined in these files. Will cause fatal errors if not globally defined.
**Severity:** Medium

### 12. Array Modification Without Bounds Check
**File:** `/home/herbert/pad/pad/handling/types/first.php`
**Line:** 3
**Description:** Compares `count($padData[$pad])` with `$padHandCnt` but doesn't validate that `$padData[$pad]` is an array.
**Severity:** Medium

### 13. Potential Division by Zero
**File:** `/home/herbert/pad/pad/handling/types/page.php`
**Line:** 6
**Description:** If `$padHandRows` is 0 (despite being cast to int), line 6 will cause division by zero error.
**Severity:** Medium

### 14. Missing Constant Validation
**File:** `/home/herbert/pad/pad/handling/types/sort.php`
**Line:** 33, 39
**Description:** `constant()` is called with dynamically built constant names. If the constant doesn't exist, it will throw a warning. Should use `defined()` to check first.
**Severity:** Medium

### 15. Reference to Undefined Variable
**File:** `/home/herbert/pad/pad/handling/types/start.php`
**Line:** 4, 5, 6
**Description:** `$padPrm[$pad]` is accessed with null coalescing, but doesn't validate that `$padPrm` is an array or that `$pad` key exists before accessing nested keys.
**Severity:** Medium

### 16. Potential Type Error
**File:** `/home/herbert/pad/pad/handling/types/sort.php`
**Line:** 36
**Description:** `array_column()` is called on `$padData[$pad]` without verifying it's an array of arrays. Will fail if data structure is incorrect.
**Severity:** Medium

## Low Bugs

### 17. Missing Validation in Conditional Includes
**File:** `/home/herbert/pad/pad/handling/handling.php`
**Line:** 12
**Description:** `file_exists()` checks for handler file, but doesn't validate the constructed path for directory traversal attacks if `$padPrmName` contains malicious input.
**Severity:** Low

### 18. Inefficient Array Key Iteration
**File:** `/home/herbert/pad/pad/handling/negative/exits.php`
**Lines:** 8-10
**Description:** Uses `substr($padHandOldKey, 1)` to strip first character. This logic assumes keys start with a specific character but doesn't validate this assumption.
**Severity:** Low

### 19. Empty Return in Conditional
**File:** `/home/herbert/pad/pad/handling/types/sort.php`
**Line:** 4
**Description:** Function returns early without setting any value if array is empty, which is fine, but should be explicit about returning nothing.
**Severity:** Low

### 20. Variable Naming Confusion
**File:** `/home/herbert/pad/pad/handling/types/splice.php`
**Lines:** 5-7
**Description:** Variable name `$padExplode` shadows the function name pattern. While not an error, it's confusing.
**Severity:** Low
