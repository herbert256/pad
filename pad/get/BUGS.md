# Bug Report - /home/herbert/pad/pad/get/

## High Bugs

### 1. Undefined Function Calls
**File:** `/home/herbert/pad/pad/get/go/call.php`
**Line:** 14
**Description:** Function `padFileGet()` is called but not defined in this file. Will cause fatal error if not available globally.
**Severity:** High

### 2. Undefined Function Calls
**File:** `/home/herbert/pad/pad/get/page.php`
**Line:** 3
**Description:** Function `padAppPageCheck()` is called but not defined in this file. Will cause fatal error if not available globally.
**Severity:** High

### 3. Undefined Function Calls
**File:** `/home/herbert/pad/pad/get/include.php`
**Line:** 3
**Description:** Function `padAppIncludeCheck()` is called but not defined in this file. Will cause fatal error if not available globally.
**Severity:** High

### 4. Undefined Function Calls
**File:** `/home/herbert/pad/pad/get/content.php`
**Lines:** 6, 7, 9, 10
**Description:** Functions `padAppIncludeCheck()`, `padAppPageCheck()`, `padTypeTag()`, and `padTagAsFunction()` are called but not defined. Will cause fatal errors if not available globally.
**Severity:** High

## Medium Bugs

### 5. Undefined Variable
**File:** `/home/herbert/pad/pad/get/go/call.php`
**Line:** 3, 4
**Description:** Variable `$padGetCall` is used but not defined in this file. Expected to come from calling context but no validation exists.
**Severity:** Medium

### 6. Undefined Variable
**File:** `/home/herbert/pad/pad/get/page.php`
**Line:** 3
**Description:** Variable `$padGetName` is used but not defined in this file. No validation or error handling.
**Severity:** Medium

### 7. Undefined Variable
**File:** `/home/herbert/pad/pad/get/include.php`
**Line:** 3
**Description:** Variable `$padGetName` is used but not defined in this file. No validation or error handling.
**Severity:** Medium

### 8. Undefined Variable
**File:** `/home/herbert/pad/pad/get/content.php`
**Lines:** 3, 6, 7, 9, 10
**Description:** Variables `$padContentStore`, `$padGetName`, and `$padContent` are used but not defined or validated.
**Severity:** Medium

### 9. Undefined Constants
**Files:** All files in this directory
**Description:** Constants `APP2`, `PAD`, and `APP` are used without being defined in these files. Will cause fatal errors if not defined globally.
**Severity:** Medium

### 10. Missing Array Key Check
**File:** `/home/herbert/pad/pad/get/content.php`
**Line:** 3
**Description:** Accesses `$padContentStore[$padGetName]` without checking if `$padContentStore` is an array or if the key exists. Although `isset()` is used, if `$padContentStore` is not defined at all, it will generate a warning.
**Severity:** Medium

## Low Bugs

### 11. Missing Error Handling for file_exists
**File:** `/home/herbert/pad/pad/get/go/call.php`
**Lines:** 8, 13
**Description:** No error handling if file paths are invalid or if file reading fails after existence check passes.
**Severity:** Low

### 12. Potential Race Condition
**File:** `/home/herbert/pad/pad/get/go/call.php`
**Lines:** 8-14
**Description:** File existence is checked before reading, but file could be deleted between check and read (TOCTOU - Time-of-check to time-of-use).
**Severity:** Low
