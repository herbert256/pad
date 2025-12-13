# PAD Tag Properties Reference

This document provides a complete reference for all PAD tag properties.

## Overview

Tag properties provide contextual information during template execution. They are accessed using the `@` symbol:

```
{@propertyName}
```

Properties give access to iteration state, data, parameters, and metadata about the current tag execution context.

---

## Syntax

### Basic Property Access

```
{@property}
```

### In Conditionals

```
{@first ? <ul>}
{@last ? </ul>}
{@even ? class="even" : class="odd"}
```

### With Pipes

```
{@count | number_format}
{@key | upper}
```

---

## Iteration State Properties

Properties that indicate position within a loop iteration.

### first

Returns `TRUE` if this is the first iteration.

```
{@first}
```

**Logic:** `$padOccur[$padIdx] == 1`

**Example:**
```html
{users}
  {@first ? <table><tr><th>Name</th></tr>}
  <tr><td>{$name}</td></tr>
{/users}
```

---

### last

Returns `TRUE` if this is the last iteration.

```
{@last}
```

**Logic:** `$padKey[$padIdx] == array_key_last($padData[$padIdx])`

**Example:**
```html
{users}
  <tr><td>{$name}</td></tr>
  {@last ? </table>}
{/users}
```

---

### notFirst

Returns `TRUE` if this is NOT the first iteration.

```
{@notFirst}
```

**Logic:** `! @first`

**Example:**
```html
{items}
  {@notFirst ? , }{$name}
{/items}
```

**Output:** `item1, item2, item3`

---

### notLast

Returns `TRUE` if this is NOT the last iteration.

```
{@notLast}
```

**Logic:** `! @last`

**Example:**
```html
{items}
  {$name}{@notLast ? |}
{/items}
```

**Output:** `item1|item2|item3`

---

### border

Returns `TRUE` if this is either the first OR last iteration.

```
{@border}
```

**Logic:** `@first OR @last`

**Example:**
```html
{items}
  <div {@border ? class="highlight"}>{$name}</div>
{/items}
```

---

### middle

Returns `TRUE` if this is neither first nor last (middle items only).

```
{@middle}
```

**Logic:** `NOT @border` (neither first nor last)

**Example:**
```html
{items}
  {@middle ? <div class="middle">{$name}</div>}
{/items}
```

---

### even

Returns `TRUE` if the current occurrence number is even.

```
{@even}
```

**Logic:** `$padOccur[$padIdx] % 2 == 0`

**Example:**
```html
{rows}
  <tr class="{@even ? even : odd}">{$data}</tr>
{/rows}
```

---

### odd

Returns `TRUE` if the current occurrence number is odd.

```
{@odd}
```

**Logic:** `NOT @even`

**Example:**
```html
{rows}
  <tr class="{@odd ? odd : even}">{$data}</tr>
{/rows}
```

---

## Counter Properties

Properties that provide numeric information about the iteration.

### current

Returns the current occurrence number (1-based index).

```
{@current}
```

**Returns:** `$padOccur[$padIdx]`

**Example:**
```html
{users}
  {@current}. {$name}
{/users}
```

**Output:**
```
1. Alice
2. Bob
3. Charlie
```

---

### count

Returns the total number of items.

```
{@count}
```

**Returns:** `max(count($padData[$padIdx]), $padOccur[$padIdx])`

**Example:**
```html
{users}
  User {@current} of {@count}
{/users}
```

---

### remaining

Returns the number of iterations remaining.

```
{@remaining}
```

**Returns:** `count($padData[$padIdx]) - $padOccur[$padIdx]` (minimum 0)

**Example:**
```html
{users}
  {$name} ({@remaining} more to go)
{/users}
```

---

### done

Returns the number of completed iterations (current - 1).

```
{@done}
```

**Returns:** `$padOccur[$padIdx] - 1`

**Example:**
```html
{users}
  Processed: {@done}, Current: {$name}
{/users}
```

---

## Data Access Properties

Properties that provide access to the current data context.

### data

Returns the complete data array for the current level.

```
{@data}
```

**Returns:** `$padData[$padIdx]`

**Example:**
```html
{users}
  {-- Access the raw data array --}
  {@data toData="allUsers"}
{/users}
```

---

### key

Returns the current array key.

```
{@key}
```

**Returns:** `$padKey[$padIdx]`

**Example:**
```html
{config}
  {-- For associative arrays, get the key --}
  {@key}: {$value}
{/config}
```

**Output:**
```
host: localhost
port: 3306
user: admin
```

---

### keys

Returns an array of all keys with their values as iterable data.

```
{@keys}
```

**Returns:** Array where each item has `name` (the key) and `value`

**Example:**
```html
{data myArray}
  {@keys}
    Key: {$name}, Value: {$value}
  {/@keys}
{/data}
```

---

### fields

Returns an array of field name/value pairs from the current record.

```
{@fields}
```

**Returns:** Array where each item has `name` and `value`

**Example:**
```html
{user}
  <table>
  {@fields}
    <tr><td>{$name}</td><td>{$value}</td></tr>
  {/@fields}
  </table>
{/user}
```

**Output:**
```html
<table>
  <tr><td>id</td><td>1</td></tr>
  <tr><td>name</td><td>Alice</td></tr>
  <tr><td>email</td><td>alice@example.com</td></tr>
</table>
```

---

### firstFieldName

Returns the name of the first field in the current record.

```
{@firstFieldName}
```

**Returns:** First key from `$padCurrent[$padIdx]`

**Example:**
```html
{user}
  Primary field: {@firstFieldName}
{/user}
```

---

### firstFieldValue

Returns the value of the first field in the current record.

```
{@firstFieldValue}
```

**Returns:** First value from `$padCurrent[$padIdx]`

**Example:**
```html
{user}
  Primary value: {@firstFieldValue}
{/user}
```

---

## Tag Metadata Properties

Properties that provide information about the current tag.

### name

Returns the name of the current tag/level.

```
{@name}
```

**Returns:** `$padName[$padIdx]`

**Example:**
```html
{users}
  Processing tag: {@name}
{/users}
```

**Output:** `Processing tag: users`

---

## Parameter & Option Properties

Properties for accessing tag parameters and options.

### parameter

Returns a specific named parameter value.

```
{@parameter:paramName}
```

**Returns:** `$padPrm[$padIdx][$parm]` or `NULL`

**Example:**
```html
{myTag sort="name" limit="10"}
  Sort by: {@parameter:sort}
  Limit: {@parameter:limit}
{/myTag}
```

---

### parameters

Returns all parameters as an iterable array.

```
{@parameters}
```

**Returns:** All parameters from `$padPrm[$padIdx]`

**Example:**
```html
{myTag foo="1" bar="2"}
  {@parameters}
    {$name} = {$value}
  {/@parameters}
{/myTag}
```

---

### option

Returns a specific positional option value.

```
{@option:1}
{@option:2}
```

**Returns:** `$padOpt[$padIdx][$parm]` or `NULL`

**Example:**
```html
{myTag arg1 arg2 arg3}
  First: {@option:1}
  Second: {@option:2}
{/myTag}
```

---

### options

Returns all positional options as an iterable array.

```
{@options}
```

**Returns:** All options from `$padOpt[$padIdx]` (excluding index 0)

**Example:**
```html
{myTag opt1 opt2 opt3}
  {@options}
    Option: {$value}
  {/@options}
{/myTag}
```

---

## Variable Properties

Properties for accessing level-scoped variables.

### variable

Returns a specific level-scoped variable.

```
{@variable:varName}
```

**Returns:** `$padSetLvl[$padIdx][$parm]` or `NULL`

**Example:**
```html
{users myVar="test"}
  Variable: {@variable:myVar}
{/users}
```

---

### variables

Returns all level-scoped variables as an iterable array.

```
{@variables}
```

**Returns:** All variables from `$padSetLvl[$padIdx]`

**Example:**
```html
{users a="1" b="2"}
  {@variables}
    {$name}: {$value}
  {/@variables}
{/users}
```

---

## Property Summary Table

| Property | Type | Description |
|----------|------|-------------|
| `@first` | Boolean | Is first iteration |
| `@last` | Boolean | Is last iteration |
| `@notFirst` | Boolean | Is NOT first iteration |
| `@notLast` | Boolean | Is NOT last iteration |
| `@border` | Boolean | Is first OR last |
| `@middle` | Boolean | Is neither first nor last |
| `@even` | Boolean | Is even occurrence |
| `@odd` | Boolean | Is odd occurrence |
| `@current` | Number | Current occurrence (1-based) |
| `@count` | Number | Total item count |
| `@remaining` | Number | Items remaining |
| `@done` | Number | Items completed |
| `@data` | Array | Full data array |
| `@key` | Mixed | Current array key |
| `@keys` | Array | All keys with values |
| `@fields` | Array | Field name/value pairs |
| `@firstFieldName` | String | First field's name |
| `@firstFieldValue` | Mixed | First field's value |
| `@name` | String | Tag name |
| `@parameter:x` | Mixed | Named parameter |
| `@parameters` | Array | All parameters |
| `@option:n` | Mixed | Positional option |
| `@options` | Array | All options |
| `@variable:x` | Mixed | Level variable |
| `@variables` | Array | All variables |

---

## Common Usage Patterns

### Zebra Striping

```html
{rows}
  <tr class="{@even ? row-even : row-odd}">{$data}</tr>
{/rows}
```

### First/Last Handling

```html
{items}
  {@first ? <ul>}
  <li>{$name}</li>
  {@last ? </ul>}
{/items}
```

### Item Separators

```html
{tags}
  {$name}{@notLast ? , }
{/tags}
```

### Counter Display

```html
{users}
  <div>
    {@current} of {@count}: {$name}
    {@remaining ? ({@remaining} remaining)}
  </div>
{/users}
```

### Dynamic Field Display

```html
{record}
  <dl>
  {@fields}
    <dt>{$name}</dt>
    <dd>{$value}</dd>
  {/@fields}
  </dl>
{/record}
```

### Accessing Parameters

```html
{customTag mode="advanced" debug="true"}
  Mode: {@parameter:mode}
  Debug: {@parameter:debug}
{/customTag}
```
