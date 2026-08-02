# PAD Tag Types Reference

This document provides a complete reference for all PAD tag types.

## Overview

Every PAD tag has a type that determines how it's processed. Types are automatically detected based on the tag name, or can be explicitly specified using the `type:name` syntax.

## Type Syntax

### Automatic Detection

```
{tagName}
```

PAD automatically detects the type based on what `tagName` matches.

### Explicit Type

```
{type:name}
```

Explicitly specify the type for disambiguation.

**Examples:**
```
{users}              ← Auto-detected as 'select' type (if declared in $padSelect)
{select:users}       ← Explicitly specified as select type
{field:username}     ← Explicitly field type
{php:strtoupper}     ← Explicitly PHP function type
```

---

## Type Detection Order

When no explicit type is given, PAD checks in this order (see `padTypeTag()` and `padTypeCommon()` in `PAD/lib/type.php`):

1. **app** - Application tag in the app's `_tags/` directories
2. **common** - Tag in the shared `_common` app
3. **pad** - Built-in PAD tag in `PAD/tags/`
4. **pull** - Sequence store value
5. **bool** - Boolean store
6. **content** - Content store
7. **select** - Declared select table
8. **data** - Data store
9. **include** - Application include file
10. **property** - Tag property
11. **field** - Database field value
12. **array** - Array value
13. **parm** - Parameter value
14. **level** - Level variable
15. **constant** - PHP constant
16. **local** - Local data file
17. **script** - External script
18. **php** - PHP function
19. **sequence** - Sequence type
20. **action** - Sequence action
21. **function** - PAD function (fallback)

---

## Tag Types

### app

Loads application-specific tags from the `APP2/` directory.

```
{myCustomTag}
{app:myCustomTag}
```

**Resolution:** Checks `padAppTagCheck()` for tag in application directory.

**Files loaded:**
- `APP2/myCustomTag.php` - PHP logic
- `APP2/myCustomTag.pad` - PAD template

**Use case:** Application-specific custom tags.

---

### pad

Loads built-in PAD tags from the `PAD/tags/` directory.

```
{if}
{pad:if}
```

**Resolution:** `PAD/tags/{tagName}.php` or `.pad`

**Files loaded:**
- `PAD/tags/{name}.php` - PHP logic
- `PAD/tags/{name}.pad` - PAD template

**Use case:** Core PAD framework tags.

---

### common

Loads shared tags from the `apps/_common/` application.

```
{menu}
{common:menu}
```

**Resolution:** `padCommonCheck()` - checked after `app`, before `pad`, so a common tag overrides a built-in of the same name but not an app-local one. Can be disabled per app with `$padCommon = FALSE`.

**Use case:** Tags shared by all applications (navigation, layout helpers).

---

### data

Retrieves data from the data store (`$padDataStore`).

```
{data:myData}
{myData}  ← If myData exists in data store
```

**Resolution:** `$padDataStore[$tagName]`

**Options:**
- `print` - Output with formatting

**Example:**
```
{users toData="activeUsers"}
{data:activeUsers}
```

---

### content

Retrieves content from the content store (`$padContentStore`).

```
{content:header}
```

**Resolution:** `$padContentStore[$tagName]`

**Example:**
```
{header toContent="savedHeader"}
{content:savedHeader}
```

---

### bool

Retrieves boolean values from the boolean store (`$padBoolStore`).

```
{bool:isAdmin}
```

**Resolution:** `$padBoolStore[$tagName]`

**Returns:** The stored boolean value.

**Example:**
```
{checkAdmin toBool="isAdmin"}
{if bool:isAdmin}...{/if}
```

---

### field

Retrieves field values from the current data context.

```
{field:username}
{$username}  ← Shorthand for field
```

**Resolution:** `padFieldValue($tagName)`

**Returns:** Field value, or `NULL` if field is null.

**Example:**
```
{users}{field:email}{/users}
{users}{$email}{/users}  ← Equivalent
```

---

### property

Retrieves tag properties from parent tags.

```
{property:id}
{@id}  ← Shorthand for property
```

**Resolution:** `padTagValue($tagName)`

**Supports:** Parameter prefixing with `$padParm`.

**Example:**
```
{list}{@index}{/list}
```

---

### array

Retrieves array values by name.

```
{array:myArray}
```

**Resolution:** `padArrayValue($tagName)`

**Use case:** Access named arrays in the current context.

---

### level

Retrieves level variables from the processing stack.

```
{level:varName}
```

**Resolution:** `padGetLevelArray($tagName)`

**Use case:** Access variables from parent processing levels.

---

### constant

Retrieves PHP constants by name.

```
{constant:PHP_VERSION}
{constant:APP}
```

**Resolution:** `constant($tagName)`

**Returns:** The value of the PHP constant.

**Example:**
```
{constant:PHP_EOL}
{constant:TRUE}
```

---

### local

Loads local data files with automatic type detection.

```
{local:data/users.json}
{myfile.csv}
```

**Resolution:** `padDataFileName($tagName)`

**Supported formats:**
- `.php` - Executes PHP and returns result
- `.json` - Parsed as JSON
- `.csv` - Parsed as CSV
- `.xml` - Parsed as XML
- Other - Raw content

**Options:**
- `name` - Override data name
- `type` - Override file type
- `sandbox` - Enable sandboxed processing

**Example:**
```
{local:config/settings.json}
{data.csv name="myData" type="csv"}
```

---

### include

Includes content from application include files.

```
{include:header}
```

**Resolution:** `padAppIncludeCheck($tagName)`

**Returns:** Included file content.

---

### select

Queries database tables declared in the PAD Select subsystem.

```
{select:users}
{users}  ← If 'users' is declared in $padSelect
```

**Resolution:** `isset($padSelect[$tagName])` - tables are declared in the app's `_lib/select.php` via `$padSelect` (and relations via `$padRelations`).

**Returns:** Query results as iterable data; nested select tags join automatically through `$padRelations`.

**Example:**
```
{users $id=5}
  {$username}
{/users}
```

See [DATABASE.md](../DATABASE.md) for the full Select subsystem reference.

---

### function

Executes PAD functions as tags.

```
{function:trim}content{/function:trim}
{trim}content{/trim}
```

**Resolution:** `padFunctionAsTag($tagName)`

**Behavior:**
- Supports start/end tag pairs
- Content is passed as input value
- Options become function parameters

**Example:**
```
{upper}hello{/upper}  →  HELLO
{replace 'a' 'b'}aaa{/replace}  →  bbb
```

---

### php

Calls PHP built-in functions directly.

```
{php:strtoupper 'hello'}
{php:date 'Y-m-d'}
```

**Resolution:** `function_exists($tagName)`

**Implementation:** `call_user_func_array($tagName, $parameters)`

**Example:**
```
{php:strlen 'hello'}  →  5
{php:array_sum (1,2,3)}  →  6
```

---

### script

Executes external shell scripts.

```
{script:myscript}
{script:processor.sh arg1 arg2}
```

**Resolution:** `padScriptCheck($tagName)`

**Features:**
- Arguments are escaped with `escapeshellarg()`
- Supports glob patterns for script selection
- Captures stdout output
- Error code checking and reporting

**Returns:** Script output (stdout).

**Example:**
```
{script:generate.sh template="main"}
```

---

## Sequence Types

These types integrate with the PAD sequence subsystem.

### sequence

Accesses sequence type definitions.

```
{sequence:fibonacci}
```

**Resolution:** `file_exists(PT . $tagName)`

**Implementation:** Delegates to `PQ/start/types/sequence.php`.

---

### action

Executes sequence actions.

```
{action:sum}
{mySequence:sum}
```

**Resolution:** `file_exists(PQ/actions/types/$tagName.php)`

**Implementation:** Delegates to `PQ/start/types/action.php`.

---

### pull

Retrieves values from the sequence store.

```
{pull:mySequence}
{mySequence}  ← If exists in pqStore
```

**Resolution:** `isset($pqStore[$tagName])`

**Implementation:** Delegates to `PQ/start/types/pull.php`.

---

### flag

Handles sequence flags.

```
{flag:myFlag}
```

**Resolution:** `isset($padBoolStore[$tagName])`

**Implementation:** Delegates to `PQ/start/types/flag.php`.

---

### make

Creates new sequence values.

```
{make:fibonacci}
{fibonacci:make}
```

**Resolution:** Sequence exists + make type available.

**Implementation:** Delegates to `PQ/start/types/make.php`.

---

### keep

Keeps/stores sequence values.

```
{keep:mySequence}
```

**Implementation:** Delegates to `PQ/start/types/keep.php`.

---

### remove

Removes sequence values.

```
{remove:mySequence}
```

**Implementation:** Delegates to `PQ/start/types/remove.php`.

---

## Type Summary Table

| Type | Source | Description |
|------|--------|-------------|
| `app` | App `_tags/` | Application-specific tags |
| `common` | apps/_common/ | Shared tags |
| `pad` | PAD/tags/ | Built-in PAD tags |
| `data` | $padDataStore | Stored data arrays |
| `content` | $padContentStore | Stored content strings |
| `bool` | $padBoolStore | Boolean flags |
| `field` | Current data | Field values |
| `property` | Parent tag | Tag properties |
| `array` | Arrays | Named arrays |
| `level` | Stack | Level variables |
| `constant` | PHP | PHP constants |
| `local` | Files | Local data files |
| `include` | APP | Include files |
| `select` | $padSelect | Declared select tables |
| `function` | PAD/functions/ | PAD functions |
| `php` | PHP | PHP functions |
| `script` | Scripts | Shell scripts |
| `sequence` | PQ | Sequence types |
| `action` | PQ | Sequence actions |
| `pull` | pqStore | Sequence values |
| `flag` | pqStore | Sequence flags |
| `make` | PQ | Sequence creation |
| `keep` | PQ | Sequence storage |
| `remove` | PQ | Sequence removal |

---

## Type Handler Files

Each type has a corresponding handler in `PAD/types/`:

```
types/
├── action.php      → Sequence action
├── app.php         → Application tag
├── array.php       → Array value
├── bool.php        → Boolean store
├── common.php      → Shared _common tag
├── constant.php    → PHP constant
├── content.php     → Content store
├── data.php        → Data store
├── field.php       → Field value
├── flag.php        → Sequence flag
├── function.php    → PAD function
├── include.php     → Include file
├── keep.php        → Sequence keep
├── level.php       → Level variable
├── local.php       → Local file
├── make.php        → Sequence make
├── pad.php         → PAD tag
├── php.php         → PHP function
├── property.php    → Tag property
├── pull.php        → Sequence pull
├── remove.php      → Sequence remove
├── script.php      → Shell script
├── select.php      → Select table query
├── sequence.php    → Sequence type
└── _go/
    ├── local.php   → Local file processing
    └── tag.php     → Tag file loading
```

---

## Examples

### Mixed Type Usage

```html
{users where="active=1" toData="activeUsers"}

{if data:activeUsers}
  <h2>Active Users ({php:count data:activeUsers})</h2>
  {data:activeUsers}
    <div>{field:name} - {field:email}</div>
  {/data:activeUsers}
{else}
  <p>No active users</p>
{/if}

<footer>{constant:APP_VERSION}</footer>
```

### Explicit Type Disambiguation

```html
{-- 'date' could be a field, function, or PHP function --}
{field:date}          ← Get date field value
{function:date}       ← Use PAD date function
{php:date 'Y-m-d'}    ← Use PHP date function
```

### Sequence Operations

```html
{fibonacci:make 1 10}
{fibonacci:sum}
{fibonacci:pull}
{fibonacci:remove}
```
