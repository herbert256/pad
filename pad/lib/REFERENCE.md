# PAD Library Functions Reference

This document provides a complete reference for all PAD PHP library functions.

## Overview

The PAD library (`lib/`) contains PHP functions that power the PAD framework. These functions handle everything from data processing and field access to file operations and error handling.

---

## Navigation & Redirection

Functions for page navigation and URL handling.

| Function | File | Description |
|----------|------|-------------|
| `padRedirect($go, $vars)` | api.php | Redirect to URL with optional variables |
| `padRestart($go, $vars)` | api.php | Schedule page restart |
| `padFastLink($page, $vars)` | api.php | Create fast link with serialized variables |

### padRedirect

Redirects to a URL, appending session and request IDs.

```php
padRedirect('/users/edit');
padRedirect('/search', ['q' => 'test']);
padRedirect('SELF://page');  // Same host
```

### padRestart

Schedules a restart to another page (processed after current request).

```php
padRestart('/dashboard');
```

---

## Field Access Functions

Functions for accessing field values, options, and properties.

| Function | File | Description |
|----------|------|-------------|
| `padFieldCheck($parm, $lvl)` | field/field.php | Check if field exists |
| `padFieldValue($parm, $lvl)` | field/field.php | Get field value |
| `padFieldNull($parm)` | field/field.php | Check if field is NULL |
| `padArrayCheck($parm, $lvl)` | field/field.php | Check if array field exists |
| `padArrayValue($parm, $lvl)` | field/field.php | Get array field value |
| `padOptCheck($parm)` | field/field.php | Check if option exists |
| `padOptValue($parm)` | field/field.php | Get option value |
| `padTagCheck($parm)` | field/field.php | Check if tag property exists |
| `padTagValue($parm, $lvl)` | field/field.php | Get tag property value |
| `padRawValue($parm)` | field/field.php | Get raw value (escaped braces) |
| `padUrlValue($parm)` | field/field.php | Get URL-encoded value |

### padFieldValue

Gets a field value from the current data context.

```php
$name = padFieldValue('username');
$email = padFieldValue('user:email');  // From specific level
```

### padOptValue

Gets an option/parameter value from the current tag.

```php
$sort = padOptValue('sort');
$limit = padOptValue('limit');
```

---

## Data Processing Functions

Functions for processing and normalizing data.

| Function | File | Description |
|----------|------|-------------|
| `padData($input, $type, $name)` | data.php | Convert input to PAD data array |
| `padDataName($name)` | data.php | Determine data name for fields |
| `padDataForcePad($data)` | values.php | Force data into PAD format |
| `padToArray($xxx)` | values.php | Convert object/resource to array |
| `padDefaultData()` | values.php | Get default data structure |
| `padIsDefaultData($data)` | values.php | Check if data is default |

### padData

Converts various inputs (arrays, JSON, XML, objects) into PAD-compatible data arrays.

```php
$data = padData($jsonString);
$data = padData($array, 'json', 'users');
```

### padDataForcePad

Ensures data is in proper PAD format (1-indexed arrays with named fields).

```php
$normalized = padDataForcePad($rawArray);
```

---

## Database Functions

Functions for database operations.

| Function | File | Description |
|----------|------|-------------|
| `padDb($sql, $vars)` | db.php | Execute SQL on PAD database |
| `db($sql, $vars)` | db.php | Execute SQL on application database |
| `padDbConnect($host, $user, $pass, $db)` | db.php | Create database connection |
| `padTable($table, $unionBuild)` | table.php | Query table with options |
| `padTableWhere($where, $fields, $table)` | table.php | Build WHERE clause |
| `padTableGet($relation, $where)` | table.php | Get table data with relation |

### padDb / db

Execute SQL queries with variable substitution.

```php
// Variables are escaped automatically
$users = db("select * from users where status={0}", ['active']);

// Insert with auto-escape
db("insert into logs values('{0}','{1}')", [$action, $message]);

// Raw SQL (no escape) with 'x' prefix
db("select * from {x0}", [$tableName]);
```

**SQL Command Types:**
- `select` / `array` - Returns array of records
- `record` - Returns single record
- `field` - Returns single field value
- `check` - Returns row count (exists check)
- `insert` - Returns insert ID or row count
- `update` / `delete` - Returns affected rows

### padTable

Query a database table with PAD options.

```php
$users = padTable('users');
```

---

## File Operations

Functions for reading and writing files.

| Function | File | Description |
|----------|------|-------------|
| `padFileGet($file, $default)` | file.php | Read file contents |
| `padFilePut($file, $data, $append)` | file.php | Write file contents |
| `padFileCheck($file)` | file.php | Validate file path |

### padFileGet

Reads file contents safely with path validation.

```php
$content = padFileGet('templates/header.html');
$json = padFileGet('config/settings.json', '{}');  // With default
$input = padFileGet('php://input');  // Read POST body
```

### padFilePut

Writes data to files in the DAT directory.

```php
padFilePut('cache/data.json', $data);
padFilePut('logs/app.log', $message, TRUE);  // Append
```

---

## Content Functions

Functions for content manipulation and merging.

| Function | File | Description |
|----------|------|-------------|
| `padContentMerge(&$true, &$false, $new, $cond)` | content.php | Merge content based on condition |
| `padContentSet($base, $new)` | content.php | Set/merge content |
| `padContentElse($input, &$before, &$after)` | content.php | Split at {else} |
| `padContentBeforeAfter($input, &$before, &$after)` | content.php | Split at @content@ |
| `padContent($content)` | checks.php | Process content string |
| `padMakeContent($input)` | values.php | Create content from input |

### padContentSet

Merges content using @content@ placeholder or merge option.

```php
$result = padContentSet($base, $new);
```

---

## Type Detection Functions

Functions for detecting and validating types.

| Function | File | Description |
|----------|------|-------------|
| `padTypeCommon($type)` | type.php | Detect common type from stores |
| `padTypeTag($type, $goFunction)` | type.php | Detect tag type |
| `padTypeTagCheck($type, $item)` | type.php | Validate explicit type |
| `padTypeFunction($type, $goTag)` | type.php | Detect function type |
| `padTypeSeq($type, $item)` | type.php | Detect sequence type |
| `padContentType(&$content)` | checks.php | Detect content type (json, xml, etc.) |

### padTypeTag

Determines what type a tag name represents.

```php
$type = padTypeTag('users');  // Returns 'table', 'app', 'pad', etc.
```

---

## Validation Functions

Functions for validating names, paths, and data.

| Function | File | Description |
|----------|------|-------------|
| `padValid($name)` | valid.php | Validate tag/type name |
| `padValidFile($file)` | valid.php | Validate file path |
| `padValidVar($name)` | valid.php | Validate variable name |
| `padValidType($name)` | valid.php | Validate type name |
| `padValidTag($name)` | valid.php | Validate tag name |
| `padAtValid($part)` | valid.php | Validate @ notation part |
| `padValidStore($fld)` | checks.php | Validate store field name |

### padValid

Validates that a name is a valid PAD identifier.

```php
if (padValid($tagName)) { ... }
```

### padValidFile

Validates file path for security (no .., control chars, etc.).

```php
if (padValidFile($path)) { ... }
```

---

## Error Handling Functions

Functions for error reporting and handling.

| Function | File | Description |
|----------|------|-------------|
| `padError($error)` | error.php | Report an error |
| `padErrorThrow($type, $error, $file, $line)` | error.php | Error handler for PHP errors |
| `padErrorStop($error, $e)` | error.php | Stop execution with error |
| `padErrorLog($info)` | error.php | Log error to file |
| `padErrorFile($info)` | error.php | Write error to error file |
| `padErrorConsole($info)` | error.php | Output error to console |
| `padErrorExit($error)` | error.php | Exit with error message |
| `padErrorGet($e)` | error.php | Get exception info |

### padError

Reports an error through PAD's error handling system.

```php
padError("Database connection failed");
return padError("Invalid input: $value");
```

---

## Exit & Output Functions

Functions for ending requests and sending output.

| Function | File | Description |
|----------|------|-------------|
| `padExit($stop)` | exit.php | End request with status code |
| `padWebSend($stop)` | output.php | Send web response |
| `padWebHeaders($stop)` | output.php | Send HTTP headers |
| `padDownLoadHeaders($type, $name, $len)` | output.php | Send download headers |
| `padOutput($output)` | tidy.php | Process final output |
| `padHeader($header)` | tidy.php | Send HTTP header |

### padExit

Ends the PAD request with proper cleanup.

```php
padExit(200);  // Success
padExit(404);  // Not found
padExit(500);  // Server error
```

---

## Evaluation Functions

Functions for expression evaluation (in `eval/` subdirectory).

| Function | File | Description |
|----------|------|-------------|
| `padEval($eval, $value)` | eval/eval.php | Evaluate expression |
| `padEvalBool($eval, $value)` | eval/eval.php | Evaluate as boolean |
| `padEvalParse(&$result, $eval)` | eval/parse.php | Parse expression |
| `padEvalAfter(&$result)` | eval/after.php | Post-parse processing |
| `padEvalPipes($result, &$pipes)` | eval/pipes.php | Split on pipe operators |
| `padEvalResult($result, $value, $eval)` | eval/result.php | Compute result |
| `padEvalOpr(&$result, $myself, $start, $end)` | eval/operations.php | Execute operators |

### padEval

Evaluates a PAD expression with optional input value.

```php
$result = padEval('upper', $value);
$result = padEval('$name | trim | upper');
$result = padEval('$price * 1.1');
```

### padEvalBool

Evaluates expression and returns boolean result.

```php
if (padEvalBool('$age > 18')) { ... }
```

---

## Callback Functions

Functions for callback system.

| Function | File | Description |
|----------|------|-------------|
| `padCallbackBeforeXxx($type)` | callback.php | Initialize/exit callbacks |
| `padCallbackBeforeRow(&$row)` | callback.php | Process row in callback |

---

## Level & Processing Functions

Functions for template level processing.

| Function | File | Description |
|----------|------|-------------|
| `padLevel($value)` | level.php | Insert value at current position |
| `padPipeSplit($input)` | level.php | Split on pipe (respecting quotes) |
| `padLevelStart()` | level.php | Find level start position |
| `padLevelEnd()` | level.php | Find level end position |
| `padLevelBetween()` | level.php | Get content between braces |
| `padCommentCheck()` | level.php | Check if current is comment |
| `padGetLevelArray($tag)` | scope.php | Get level array by tag |
| `padChkLevel($tag)` | scope.php | Check if level exists |

### padLevel

Inserts a value into the output at the current processing position.

```php
padLevel($processedValue);
```

---

## Utility Functions

General utility functions.

| Function | File | Description |
|----------|------|-------------|
| `padExplode($haystack, $limit, $number)` | template.php | Smart explode with options |
| `padJson($data)` | values.php | Convert to JSON |
| `padTidy($data, $fragment)` | tidy.php | Tidy HTML output |
| `padBetween($string, $open, $close, ...)` | template.php | Extract between delimiters |
| `padSplit($needle, $haystack, &$before, &$after)` | template.php | Split string in two |
| `padMakeSafe($input, $len)` | template.php | Sanitize input |
| `padGetRange($input, $increment)` | template.php | Generate numeric range |
| `padGetList($list)` | template.php | Parse list string |

### padExplode

Enhanced explode with trimming and empty handling.

```php
$parts = padExplode('a, b, c', ',');  // ['a', 'b', 'c']
$parts = padExplode('a;b;c', ';', 2);  // ['a', 'b;c']
```

### padJson

Converts data to JSON with proper encoding.

```php
$json = padJson($data);
```

### padBetween

Extracts content between delimiters.

```php
padBetween('hello [world] there', '[', ']', $before, $between, $after);
// $before='hello ', $between='world', $after=' there'
```

---

## Encoding Functions

Functions for encoding and hashing.

| Function | File | Description |
|----------|------|-------------|
| `padMD5($input)` | encoding.php | Calculate MD5 hash |
| `padMD5Unpack($input)` | encoding.php | Unpack MD5 hash |
| `padPack($data)` | encoding.php | Serialize and compress |
| `padUnpack($data)` | encoding.php | Decompress and unserialize |
| `padBase64($string)` | encoding.php | Base64 encode |
| `padUnbase64($string)` | encoding.php | Base64 decode |
| `padZip($data)` | encoding.php | Gzip compress |
| `padUnzip($data)` | encoding.php | Gzip decompress |
| `padEscape($string)` | encoding.php | Escape special chars |
| `padUnescape($string)` | encoding.php | Unescape special chars |

---

## Random & ID Functions

Functions for generating random values and IDs.

| Function | File | Description |
|----------|------|-------------|
| `padRandomString($len)` | encoding.php | Generate random string |
| `padRandomChar()` | encoding.php | Generate random character |
| `padID()` | session.php | Generate unique ID |
| `padTimeStamp()` | time.php | Get microsecond timestamp |

### padRandomString

Generates a random alphanumeric string.

```php
$token = padRandomString(32);
$code = padRandomString(6);
```

---

## Path & Directory Functions

Functions for path manipulation.

| Function | File | Description |
|----------|------|-------------|
| `padDir()` | paths.php | Get current directory |
| `padDirs()` | paths.php | Get directory hierarchy |
| `padPath()` | paths.php | Get current path |
| `padFileName($withDir)` | paths.php | Get current filename |
| `padCorrectPath($in)` | paths.php | Normalize path separators |
| `padValidatePath($path)` | paths.php | Validate path for security |

---

## Tag & Function Helpers

Functions for tag and function processing.

| Function | File | Description |
|----------|------|-------------|
| `padFunctionAsTag($name, $myself, $parm)` | execute.php | Use function as tag |
| `padTagAsFunction($tag, $value, $parms)` | execute.php | Use tag as function |
| `padTagParm($parm, $default)` | scope.php | Get current tag parameter |
| `padDone($var, $val)` | scope.php | Mark option as done |
| `padIsDone($var)` | scope.php | Check if option is done |
| `padMakeFlag($input)` | values.php | Create boolean flag |
| `padStartAndClose($go)` | scope.php | Handle start/close tags |

### padTagParm

Gets a parameter from the current tag.

```php
$sort = padTagParm('sort', 'name');  // With default
$limit = padTagParm('limit');
```

---

## Application Check Functions

Functions for checking application resources.

| Function | File | Description |
|----------|------|-------------|
| `padAppCheck($check)` | checks.php | Check if app resource exists |
| `padAppPageCheck($check)` | checks.php | Check if page exists |
| `padAppIncludeCheck($check)` | checks.php | Check if include exists |
| `padAppTagCheck($check)` | checks.php | Check if custom tag exists |
| `padAppFunctionCheck($check)` | checks.php | Check if custom function exists |
| `padScriptCheck($check)` | checks.php | Check if script exists |
| `padDataFileName($check)` | paths.php | Check if data file exists |

---

## Dump & Debug Functions

Functions for debugging and diagnostics (in `dump.php`).

| Function | File | Description |
|----------|------|-------------|
| `padDump($error)` | dump.php | Generate debug dump |
| `padDumpToDir($info, $dir)` | dump.php | Write dump to directory |
| `padDumpLevel()` | dump.php | Dump level information |
| `padDumpStack()` | dump.php | Dump call stack |
| `padDumpGlobals()` | dump.php | Dump global variables |
| `padDumpSQL()` | dump.php | Dump SQL queries |
| `padDumpHeaders()` | dump.php | Dump HTTP headers |
| `padDumpRequest()` | dump.php | Dump request data |

### padDump

Generates a comprehensive debug dump.

```php
padDump("Something went wrong");
```

---

## CURL Functions

Functions for HTTP requests.

| Function | File | Description |
|----------|------|-------------|
| `padCurl($input)` | curl.php | Make HTTP request |
| `padNoCurl($output)` | curl.php | Handle curl-less environment |
| `padCurlOpt(&$options, $name, $value)` | curl.php | Set curl option |
| `padCurlError($output, $error)` | curl.php | Handle curl error |

### padCurl

Makes HTTP requests with extensive options.

```php
$response = padCurl('https://api.example.com/data');
$response = padCurl([
    'url' => 'https://api.example.com/post',
    'method' => 'POST',
    'data' => $postData
]);
```

---

## Duration Functions

Functions for time measurement.

| Function | File | Description |
|----------|------|-------------|
| `padDuration($start, $end)` | time.php | Calculate duration in ns |
| `padDurationHR($start, $end)` | time.php | High-resolution duration in ns |
| `padSecondTime($id)` | session.php | Check second-level timing |

---

## Global Variable Functions

Functions for managing global variables.

| Function | File | Description |
|----------|------|-------------|
| `padSetGlobalLvl($name, $value)` | scope.php | Set level-scoped global |
| `padSetGlobalOcc($name, $value)` | scope.php | Set occurrence-scoped global |
| `padResetLvl()` | scope.php | Reset level variables |
| `padResetOcc()` | scope.php | Reset occurrence variables |

---

## Page Functions

Functions for page handling.

| Function | File | Description |
|----------|------|-------------|
| `padPage($page)` | page.php | Load and process page |
| `padPageCheck($page)` | page.php | Check if page exists |
| `padPageGet($page, $qry)` | page.php | Get page content |
| `padPageAjax($page, $qry)` | page.php | Handle AJAX page request |

---

## Sequence Functions

Functions for mathematical sequences (in `sequence.php`).

| Function | File | Description |
|----------|------|-------------|
| `padTypeReverse($x)` | sequence.php | Reverse digits of number |

---

## Open/Close Balance Functions

Functions for checking balanced tags.

| Function | File | Description |
|----------|------|-------------|
| `padOpenCloseOk($string, $check)` | template.php | Check if marker is at valid position |
| `padOpenCloseList($string)` | template.php | Get list of close tags |
| `padOpenCloseCount($string, $tags)` | template.php | Count open/close balance |
| `padOpenCloseCountOne($string, $tag)` | template.php | Check single tag balance |
| `padCheckTag($tag, $string)` | template.php | Check if tag exists in string |

---

## Handling Functions

Functions for data handling operations.

| Function | File | Description |
|----------|------|-------------|
| `padHandGo(&$vars, $start, $end, $count)` | filter.php | Apply range filter to data |
| `padDataFilterGo(&$vars, $start, $end)` | filter.php | Filter data by range |

---

## Session Functions

Functions for session management.

| Function | File | Description |
|----------|------|-------------|
| `padCloseSession()` | session.php | Close session safely |
| `padAddIds($url)` | paths.php | Add session/request IDs to URL |
| `padAddGet($url, $key, $val)` | paths.php | Add GET parameter to URL |

---

## Info Functions

Functions for tracing/debugging info.

| Function | File | Description |
|----------|------|-------------|
| `padInfo()` | session.php | Get info mode status |
| `padInfoSet()` | info.php | Initialize info mode |
| `padInfoBackup()` | info.php | Backup info state |
| `padInfoRestore()` | info.php | Restore info state |

---

## Miscellaneous Functions

Other utility functions.

| Function | File | Description |
|----------|------|-------------|
| `padInclude()` | session.php | Include file handler |
| `padFileXmlTidy($file)` | tidy.php | Tidy XML file |
| `padCode($str)` | execute.php | Process code string |
| `padSandbox($str)` | execute.php | Process sandboxed code |
| `padConstant($parm)` | values.php | Get constant value |
| `padFindIdx($tag)` | scope.php | Find index for tag |
| `padSingleValue($value)` | values.php | Check for single value |
| `padSpecialValue($value)` | values.php | Check for special values |
| `padStoreCheck($store)` | checks.php | Check store exists |
| `padEmptyBuffers(&$output)` | tidy.php | Empty output buffers |
| `padCheckBuffers()` | tidy.php | Check buffer status |
| `padFieldName($parm)` | values.php | Extract field name |
| `padStrPad($field)` | checks.php | Pad string |
| `padParseOptions($parms)` | options.php | Parse option string |
| `padGetParms($type, $parms)` | parms.php | Get parameters by type |
