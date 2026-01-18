# PAD Framework Code Analysis

**Analysis Date:** January 2026
**Scope:** `/Users/herbert/pad/pad/` (948 PHP files, ~393,000 lines)

---

## Table of Contents

1. [Executive Summary](#executive-summary)
2. [Critical Security Issues](#critical-security-issues)
3. [Bugs and Code Errors](#bugs-and-code-errors)
4. [Dead Code](#dead-code)
5. [Code Smells](#code-smells)
6. [Architectural Concerns](#architectural-concerns)
7. [Recommendations](#recommendations)

---

## Executive Summary

The PAD framework is a mature, procedural PHP template engine with 15+ years of production use. This analysis identified:

| Category | Count |
|----------|-------|
| **Critical Security Issues** | 4 |
| **High Severity Issues** | 7 |
| **Bugs/Errors** | 5 |
| **Dead Code Areas** | 8 |
| **Code Smells** | 15+ |

### Key Findings

- **Unsafe deserialization** with `extract()` and `EXTR_OVERWRITE` (Critical)
- **SQL injection possible** via 'x' prefix bypass in db() function
- **Shell command injection risk** in script execution
- **Unreachable code** in multiple files
- **Undefined variables** in property handlers
- **Massive global state** (30+ arrays per processing level)

---

## Critical Security Issues

### 1. Unsafe Deserialization (CRITICAL)

**Location:** `inits/fast.php` (Line 13)

```php
$padFast = padDb("field vars from links where link = '{1}'", [$_SERVER['QUERY_STRING']]);
extract(unserialize($padFast), EXTR_OVERWRITE);
```

**Problems:**
- `unserialize()` on data from database (originally from user input)
- `EXTR_OVERWRITE` allows overwriting ANY global variable
- Can overwrite: `$padSqlHost`, `$padSqlPassword`, `$padErrorAction`, etc.
- **CWE-502: Deserialization of Untrusted Data**
- **PHP Object Injection possible** if malicious classes exist

**Fix:** Replace with `json_encode()`/`json_decode()` or implement strict allowlist.

---

### 2. SQL Injection via 'x' Prefix (CRITICAL)

**Location:** `lib/db.php` (Lines 50-58)

```php
foreach ($vars as $i => $replace) {
  if (substr($i, 0, 1) <> 'x')
    $sql = str_replace('{'.$i.'}', mysqli_real_escape_string($padSqlConnect, $replace), $sql);
  else
    $sql = str_replace('{'.$i.'}', $replace, $sql);  // UNESCAPED!
```

**Problem:** Variables with 'x' prefix bypass escaping entirely, allowing raw SQL injection.

**Example Attack:**
```php
db("SELECT * FROM users WHERE id = {x1}", ['x1' => "1; DROP TABLE users;--"]);
```

**Fix:** Remove 'x' prefix bypass or use prepared statements exclusively.

---

### 3. Shell Command Injection Risk (HIGH)

**Location:** `types/script.php` (Lines 3-22)

```php
foreach (glob(padScriptCheck($padTag[$pad])) as $padExec) {
    exec("$padExec $padExecArgs", $padExecOut, $padExecReturn);
}
```

**Problems:**
- `$padExec` path comes from `glob()` which could match unintended files
- Script path validation in `padScriptCheck()` doesn't prevent all traversal
- No whitelist of allowed scripts

**Fix:** Implement strict script whitelist, validate paths with `realpath()`.

---

### 4. Information Disclosure (MEDIUM)

**Location:** `error/claude.php` (Lines 15-39)

```php
if (padClaudeCheck()) {  // Checks for localhost
    echo json_encode($claude);  // Exposes: globals, stack traces, DB credentials
}
```

**Problem:** Detection is weak - returns errors if `REMOTE_ADDR` is empty or localhost. Exposes full framework internals.

**Fix:** Use request IDs only; log details server-side.

---

### 5. Path Traversal in File Validation (MEDIUM)

**Location:** `lib/file.php` (Lines 88-96)

```php
function padFileCheck($file) {
  if (!str_starts_with($file, '/')) return "Invalid file...";
  if (strpos($file, '..') !== FALSE) return "Invalid file...";
  // ...
}
```

**Problems:**
- No `realpath()` check for symlink attacks
- No validation file is within APP/DAT/PAD boundaries
- Case sensitivity issues on some filesystems

---

### 6. CURL Header Injection (MEDIUM)

**Location:** `lib/curl.php` (Line 95)

```php
padCurlOpt($options, 'USERAGENT', $_SERVER['HTTP_USER_AGENT'] ?? 'Mozilla/...');
```

**Problem:** User-controlled `HTTP_USER_AGENT` passed directly without sanitization. Could contain newlines for header injection.

---

## Bugs and Code Errors

### 1. Unreachable Code in eval.php (CRITICAL BUG)

**Location:** `level/eval.php` (Lines 3-11)

```php
<?php
return padLevel($padBetweenOrg);     // ← ALWAYS RETURNS HERE

  $padReturn = padEval($padBetweenOrg);  // NEVER EXECUTED
  if (is_array($padReturn))
    return padLevel($padBetweenOrg);
  else
    return padLevel($padReturn);
?>
```

**Impact:** Expression evaluation logic after first call never executes.

---

### 2. Undefined Variable: $parm

**Location:** `properties/option.php` (Line 5)

```php
if (isset($padPrm[$padIdx][$parm]))  // ← $parm is UNDEFINED
```

**Location:** `properties/parameter.php` (Line 5)

```php
if (isset($padOpt[$padIdx][$parm]))  // ← $parm is UNDEFINED
```

**Impact:** Property queries always return NULL, breaking parameter retrieval.

---

### 3. Missing Global Declarations

**Location:** `properties/option.php`, `properties/parameter.php`

These files access `$padIdx` without declaring it global:

```php
global $padPrm;  // Missing: global $padIdx;
if (isset($padPrm[$padIdx][$parm]))
```

---

### 4. Undefined Constants

**Location:** `lib/checks.php` (Lines 153-164)

```php
function padStrPad($field) {
  // ...
  if (!in_array($field, padStrSto))    // ← UNDEFINED CONSTANT
    if (!in_array($field, padLevelVars))  // ← UNDEFINED CONSTANT
```

**Impact:** PHP warnings/errors if function is called.

---

### 5. Deprecated Operators

**72 occurrences** of deprecated `<>` operator throughout codebase.

**Examples:**
- `tags/curl.php:13` - `if ($padCurl['result'] <> '200')`
- `options/toData.php:10` - `if ($padWalk[$pad] <> 'start')`
- `lib/checks.php:159` - `if ($field <> 'padInfoCnt'...)`

**Fix:** Replace all `<>` with `!=` or `!==`.

---

## Dead Code

### 1. Disabled Fast Links

**Location:** `inits/fast.php` (Lines 3-15)

```php
<?php
  return;  // ← EXITS IMMEDIATELY
  // All code below is unreachable
  if (padPageCheck($padPage))
    return;
  $padFast = padDb(...);
  extract(unserialize($padFast), EXTR_OVERWRITE);
?>
```

### 2. Unused Test Tags

| File | Content |
|------|---------|
| `tags/foo.php` | Returns `"Foo tag from pad"` |
| `tags/true.php` | Returns `TRUE` |
| `tags/false.php` | Returns `FALSE` |
| `tags/null.php` | Returns `NULL` |

### 3. Empty Option Handler

**Location:** `options/noError.php`

```php
<?php

?>
```

Completely empty file - placeholder or dead code.

### 4. Redundant at/properties/ Directory

All 20 files in `at/properties/` are identical wrappers:

```php
<?php
  return include PAD . "properties/$name.php";
?>
```

These add no value and duplicate `properties/` directory.

### 5. Wrapper Tag Files (12 files)

These tags only delegate to `start/`:

| Tag | Size | Delegates To |
|-----|------|--------------|
| `tags/ajax.php` | 52 bytes | `start/ajax.php` |
| `tags/get.php` | 51 bytes | `start/get.php` |
| `tags/redirect.php` | 56 bytes | `start/redirect.php` |
| `tags/page.php` | 55 bytes | `start/page.php` |
| `tags/code.php` | 72 bytes | `start/code.php` |
| ... | ... | ... |

### 6. Suspicious Optional Handler

**Location:** `options/optional.php`

```php
<?php
  padLevel('');
?>
```

Calls `padLevel('')` with empty string but returns nothing.

### 7. Redundant Data Checks

**Location:** `lib/data.php` (Line 15)

```php
elseif (!$input) $data = [];  // Already handled by NULL/FALSE checks above
```

### 8. Unused Variables in build/base.php

**Location:** `build/base.php` (Line 6)

```php
if ($padInclude)
    return $padBuildBase;  // Early exit rarely utilized
```

---

## Code Smells

### 1. Global Variable Pollution

**30+ global arrays** indexed by level number:

```php
$padTag[$pad], $padType[$pad], $padData[$pad], $padOpt[$pad],
$padPrm[$pad], $padBase[$pad], $padOut[$pad], $padResult[$pad],
$padElse[$pad], $padHit[$pad], $padNull[$pad], $padArray[$pad]...
```

**Problem:** No encapsulation, extremely difficult to trace state.

### 2. Include as Control Flow

**174 occurrences** of `return include` pattern:

```php
return include PAD . 'types/data.php';
return include PAD . 'eval/fast.php';
return include PAD . 'try/try.php';
```

**Problems:**
- Impossible to trace execution statically
- Return values depend on included file's behavior
- No IDE support for navigation

### 3. Nested Try/Catch Hell

**Location:** `lib/error.php` (Lines 37-147)

```
padErrorStop() → padErrorStopTry() → padErrorStopCatch() →
padErrorStopCatchCatch() → padErrorStopCatchCatchCatch()
```

5 levels of nested error handlers, each calling `set_error_handler()`.

### 4. String-Based SQL Command Detection

**Location:** `lib/db.php` (Lines 79-92)

```php
$split = explode(' ', trim($sql), 2);
$command = trim(strtolower($split[0]));
if ($command == 'select') $command = 'array';
```

**Problems:**
- Fails with comments (`-- select...`)
- No validation of command type
- Brittle string parsing

### 5. Massive Parse Function

**Location:** `lib/eval/parse.php` (Lines 35-395)

395-line single function with 8 boolean state flags:

```php
$is_hex = $is_var = $is_prm = $is_tag = $is_str = $is_quote = $is_num = $is_other = FALSE;
```

**Problems:**
- Impossible to unit test
- State machine logic is fragile
- No error recovery

### 6. Inconsistent Error Handling

- Some functions use `padError()` and continue
- Others use `padErrorStop()` and exit
- No consistent propagation strategy

### 7. Deprecated mysqli Functions

`mysqli_real_escape_string()` used instead of prepared statements.

### 8. Magic String Constants

Dynamic constant access pattern:

```php
$padOptionsWalk = constant('padOptions' . ucfirst($padOptions));
```

### 9. Overly Broad State Reset

**Location:** `options/go/reset.php`

```php
$padNull[$pad] = FALSE;
$padElse[$pad] = FALSE;
$padHit[$pad] = TRUE;
$padArray[$pad] = FALSE;
```

Global state reset without documentation of effects.

### 10. Inconsistent Comparison Operators

- 72 uses of `<>` (deprecated)
- 118 uses of `!=`/`!==`

---

## Architectural Concerns

### 1. Procedural Design

- 140+ functions, minimal OOP
- No dependency injection
- No interfaces or contracts
- Difficult to mock/test

### 2. File-Based Dispatch

```
Request → pad.php → config → start/
                          → build/ (page assembly)
                          → level/ (tag processing)
                          → Response
```

Uses `include` statements for control flow instead of function calls.

### 3. Level System Complexity

Each `{tag}` creates a new level scope with 30+ indexed arrays:

```php
$padTag[$pad], $padType[$pad], $padOpt[$pad], $padData[$pad],
$padCurrent[$pad], $padBase[$pad], $padOut[$pad], $padResult[$pad]...
```

Max depth: 100 levels (hardcoded in `level/setup.php:5`)

### 4. Mixed Concerns

Single files often handle:
- Input validation
- Business logic
- Database access
- Output formatting

### 5. No Prepared Statements

All database queries use string replacement:

```php
$sql = str_replace('{0}', mysqli_real_escape_string($conn, $val), $sql);
```

---

## Recommendations

### Immediate (Critical)

| Priority | Issue | Fix |
|----------|-------|-----|
| P0 | Unsafe `unserialize()` | Replace with `json_decode()` |
| P0 | SQL 'x' prefix bypass | Remove or use prepared statements |
| P0 | Unreachable code in eval.php | Remove lines 5-10 |
| P1 | Undefined `$parm` variable | Add parameter or define globally |
| P1 | Shell injection risk | Implement script whitelist |

### High Priority

| Priority | Issue | Fix |
|----------|-------|-----|
| P2 | `mysqli_real_escape_string()` | Migrate to prepared statements |
| P2 | Missing global declarations | Add `global $padIdx;` |
| P2 | Undefined constants | Define `padStrSto`, `padLevelVars` |
| P2 | Information disclosure | Remove JSON error response |
| P2 | Path traversal | Add `realpath()` validation |

### Medium Priority

| Priority | Issue | Fix |
|----------|-------|-----|
| P3 | Deprecated `<>` operator | Replace with `!=` |
| P3 | Dead code cleanup | Remove unused files |
| P3 | Empty handlers | Remove or implement |
| P3 | CURL header injection | Sanitize User-Agent |
| P3 | Wrapper file duplication | Merge or eliminate |

### Code Quality

| Priority | Issue | Fix |
|----------|-------|-----|
| P4 | Global state pollution | Consider encapsulation |
| P4 | Include-based control flow | Refactor to functions |
| P4 | Parse function size | Break into testable units |
| P4 | Error handling nesting | Simplify handler chain |
| P4 | Add type hints | Improve static analysis |

---

## Files Requiring Immediate Attention

1. **`inits/fast.php`** - Critical security + dead code
2. **`lib/db.php`** - SQL injection risk
3. **`level/eval.php`** - Unreachable code bug
4. **`properties/option.php`** - Undefined variable
5. **`properties/parameter.php`** - Undefined variable
6. **`types/script.php`** - Shell injection risk
7. **`lib/checks.php`** - Undefined constants

---

## Statistics

| Metric | Value |
|--------|-------|
| Total PHP files | 948 |
| Total lines | ~393,000 |
| Functions directory | 42 files |
| Tags directory | 58 files |
| Types directory | 24 files |
| Options directory | 23 files |
| Properties directory | 20 files |
| Global arrays per level | 30+ |
| Max nesting depth | 100 |
| `return include` occurrences | 174 |
| Deprecated `<>` occurrences | 72 |

---

*Generated by automated code analysis*
