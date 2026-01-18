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
{users}              ← Auto-detected as 'table' type
{table:users}        ← Explicitly specified as table type
{field:username}     ← Explicitly field type
{php:strtoupper}     ← Explicitly PHP function type
```

---

## Type Detection Order

When no explicit type is given, PAD checks in this order:

1. **app** - Application tag in `APP2/`
2. **pad** - Built-in PAD tag in `PAD/tags/`
3. **pull** - Sequence store value
4. **flag** - Boolean flag store
5. **content** - Content store
6. **data** - Data store
7. **include** - Application include file
8. **field** - Database field value
9. **property** - Tag property
10. **array** - Array value
11. **parm** - Parameter value
12. **level** - Level variable
13. **constant** - PHP constant
14. **local** - Local data file
15. **script** - External script
16. **php** - PHP function
17. **page** - Application page
18. **sequence** - Sequence type
19. **action** - Sequence action
20. **function** - PAD function (fallback)

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

### table

Queries database tables.

```
{table:users}
{users}  ← If 'users' is a known table
```

**Resolution:** `padTable($tagName)`

**Returns:** Query results as array.

**Example:**
```
{table:users where="active=1"}
  {$name}
{/table:users}
```

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

### page

Loads application pages.

```
{page:home}
{xyz:dashboard}
```

**Resolution:** `padAppPageCheck($tagName)`

**Returns:** Page content via `PAD/get/page.php`.

---

### xyz

Alias for page type.

```
{xyz:pageName}
```

**Implementation:** Includes `PAD/get/page.php`.

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
| `app` | APP2/ | Application-specific tags |
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
| `table` | Database | Table queries |
| `function` | PAD/functions/ | PAD functions |
| `php` | PHP | PHP functions |
| `script` | Scripts | Shell scripts |
| `page` | APP | Application pages |
| `xyz` | APP | Page alias |
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
├── sequence.php    → Sequence type
├── table.php       → Database table
├── xyz.php         → Page type
└── go/
    ├── local.php   → Local file processing
    ├── table.php   → Table processing
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
