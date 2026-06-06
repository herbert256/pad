# PAD Tag Properties Reference

This document provides a complete reference for all PAD tag properties.

## Overview

Tag properties provide contextual information during template execution. They are accessed using the `property@tag` syntax:

```
{property@tagname}
```

Where `tagname` is the name of the iteration/loop context and `property` is the information you want to access.

---

## Syntax

### Basic Property Access

```
{first@users}
{count@items}
{current@rows}
```

### In Conditionals

```
{if first@users}<ul>{/if}
{if last@users}</ul>{/if}
```

### Ternary Conditionals

```
{even@rows ? class="even" : class="odd"}
```

---

## Iteration State Properties

Properties that indicate position within a loop iteration.

### first

Returns `TRUE` if this is the first iteration.

```
{first@tagname}
```

**Logic:** `$padOccur[$padIdx] == 1`

**Example:**
```html
{users}
  {if first@users}<table><tr><th>Name</th></tr>{/if}
  <tr><td>{$name}</td></tr>
{/users}
```

---

### last

Returns `TRUE` if this is the last iteration.

```
{last@tagname}
```

**Logic:** `$padKey[$padIdx] == array_key_last($padData[$padIdx])`

**Example:**
```html
{users}
  <tr><td>{$name}</td></tr>
  {if last@users}</table>{/if}
{/users}
```

---

### notFirst

Returns `TRUE` if this is NOT the first iteration.

```
{notFirst@tagname}
```

**Logic:** `! first@tagname`

**Example:**
```html
{items}
  {if notFirst@items}, {/if}{$name}
{/items}
```

**Output:** `item1, item2, item3`

---

### notLast

Returns `TRUE` if this is NOT the last iteration.

```
{notLast@tagname}
```

**Logic:** `! last@tagname`

**Example:**
```html
{items}
  {$name}{if notLast@items}|{/if}
{/items}
```

**Output:** `item1|item2|item3`

---

### border

Returns `TRUE` if this is either the first OR last iteration.

```
{border@tagname}
```

**Logic:** `first@tagname OR last@tagname`

**Example:**
```html
{items}
  <div {if border@items}class="highlight"{/if}>{$name}</div>
{/items}
```

---

### middle

Returns `TRUE` if this is neither first nor last (middle items only).

```
{middle@tagname}
```

**Logic:** `NOT border@tagname` (neither first nor last)

**Example:**
```html
{items}
  {if middle@items}<div class="middle">{$name}</div>{/if}
{/items}
```

---

### even

Returns `TRUE` if the current occurrence number is even.

```
{even@tagname}
```

**Logic:** `$padOccur[$padIdx] % 2 == 0`

**Example:**
```html
{rows}
  <tr class="{even@rows ? even : odd}">{$data}</tr>
{/rows}
```

---

### odd

Returns `TRUE` if the current occurrence number is odd.

```
{odd@tagname}
```

**Logic:** `NOT even@tagname`

**Example:**
```html
{rows}
  <tr class="{odd@rows ? odd : even}">{$data}</tr>
{/rows}
```

---

## Counter Properties

Properties that provide numeric information about the iteration.

### current

Returns the current occurrence number (1-based index).

```
{current@tagname}
```

**Returns:** `$padOccur[$padIdx]`

**Example:**
```html
{users}
  {current@users}. {$name}
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
{count@tagname}
```

**Returns:** `max(count($padData[$padIdx]), $padOccur[$padIdx])`

**Example:**
```html
{users}
  User {current@users} of {count@users}
{/users}
```

---

### remaining

Returns the number of iterations remaining.

```
{remaining@tagname}
```

**Returns:** `count($padData[$padIdx]) - $padOccur[$padIdx]` (minimum 0)

**Example:**
```html
{users}
  {$name} ({remaining@users} more to go)
{/users}
```

---

### done

Returns the number of completed iterations (current - 1).

```
{done@tagname}
```

**Returns:** `$padOccur[$padIdx] - 1`

**Example:**
```html
{users}
  Processed: {done@users}, Current: {$name}
{/users}
```

---

## Data Access Properties

Properties that provide access to the current data context.

### data

Returns the complete data array for the current level.

```
{data@tagname}
```

**Returns:** `$padData[$padIdx]`

**Example:**
```html
{users}
  {data@users toData="allUsers"}
{/users}
```

---

### key

Returns the current array key.

```
{key@tagname}
```

**Returns:** `$padKey[$padIdx]`

**Example:**
```html
{config}
  {key@config}: {$value}
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
{keys@tagname}
```

**Returns:** Array where each item has `name` (the key) and `value`

**Example:**
```html
{data myArray}
  {keys@myArray}
    Key: {$name}, Value: {$value}
  {/keys}
{/data}
```

---

### fields

Returns an array of field name/value pairs from the current record.

```
{fields@tagname}
```

**Returns:** Array where each item has `name` and `value`

**Example:**
```html
{user}
  <table>
  {fields@user}
    <tr><td>{$name}</td><td>{$value}</td></tr>
  {/fields}
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
{firstFieldName@tagname}
```

**Returns:** First key from `$padCurrent[$padIdx]`

**Example:**
```html
{user}
  Primary field: {firstFieldName@user}
{/user}
```

---

### firstFieldValue

Returns the value of the first field in the current record.

```
{firstFieldValue@tagname}
```

**Returns:** First value from `$padCurrent[$padIdx]`

**Example:**
```html
{user}
  Primary value: {firstFieldValue@user}
{/user}
```

---

## Tag Metadata Properties

Properties that provide information about the current tag.

### name

Returns the name of the current tag/level.

```
{name@tagname}
```

**Returns:** `$padName[$padIdx]`

**Example:**
```html
{users}
  Processing tag: {name@users}
{/users}
```

**Output:** `Processing tag: users`

---

## Parameter & Option Properties

Properties for accessing tag parameters and options.

### parameter

Returns a specific named parameter value.

```
{parameter:paramName@tagname}
```

**Returns:** `$padPrm[$padIdx][$parm]` or `NULL`

**Example:**
```html
{myTag sort="name" limit="10"}
  Sort by: {parameter:sort@myTag}
  Limit: {parameter:limit@myTag}
{/myTag}
```

---

### parameters

Returns all parameters as an iterable array.

```
{parameters@tagname}
```

**Returns:** All parameters from `$padPrm[$padIdx]`

**Example:**
```html
{myTag foo="1" bar="2"}
  {parameters@myTag}
    {$name} = {$value}
  {/parameters}
{/myTag}
```

---

### option

Returns a specific positional option value.

```
{option:1@tagname}
{option:2@tagname}
```

**Returns:** `$padOpt[$padIdx][$parm]` or `NULL`

**Example:**
```html
{myTag arg1 arg2 arg3}
  First: {option:1@myTag}
  Second: {option:2@myTag}
{/myTag}
```

---

### options

Returns all positional options as an iterable array.

```
{options@tagname}
```

**Returns:** All options from `$padOpt[$padIdx]` (excluding index 0)

**Example:**
```html
{myTag opt1 opt2 opt3}
  {options@myTag}
    Option: {$value}
  {/options}
{/myTag}
```

---

## Variable Properties

Properties for accessing level-scoped variables.

### variable

Returns a specific level-scoped variable.

```
{variable:varName@tagname}
```

**Returns:** `$padSetLvl[$padIdx][$parm]` or `NULL`

**Example:**
```html
{users myVar="test"}
  Variable: {variable:myVar@users}
{/users}
```

---

### variables

Returns all level-scoped variables as an iterable array.

```
{variables@tagname}
```

**Returns:** All variables from `$padSetLvl[$padIdx]`

**Example:**
```html
{users a="1" b="2"}
  {variables@users}
    {$name}: {$value}
  {/variables}
{/users}
```

---

## Property Summary Table

| Property | Type | Description |
|----------|------|-------------|
| `first@tag` | Boolean | Is first iteration |
| `last@tag` | Boolean | Is last iteration |
| `notFirst@tag` | Boolean | Is NOT first iteration |
| `notLast@tag` | Boolean | Is NOT last iteration |
| `border@tag` | Boolean | Is first OR last |
| `middle@tag` | Boolean | Is neither first nor last |
| `even@tag` | Boolean | Is even occurrence |
| `odd@tag` | Boolean | Is odd occurrence |
| `current@tag` | Number | Current occurrence (1-based) |
| `count@tag` | Number | Total item count |
| `remaining@tag` | Number | Items remaining |
| `done@tag` | Number | Items completed |
| `data@tag` | Array | Full data array |
| `key@tag` | Mixed | Current array key |
| `keys@tag` | Array | All keys with values |
| `fields@tag` | Array | Field name/value pairs |
| `firstFieldName@tag` | String | First field's name |
| `firstFieldValue@tag` | Mixed | First field's value |
| `name@tag` | String | Tag name |
| `parameter:x@tag` | Mixed | Named parameter |
| `parameters@tag` | Array | All parameters |
| `option:n@tag` | Mixed | Positional option |
| `options@tag` | Array | All options |
| `variable:x@tag` | Mixed | Level variable |
| `variables@tag` | Array | All variables |

---

## Common Usage Patterns

### Zebra Striping

```html
{rows}
  <tr class="{even@rows ? row-even : row-odd}">{$data}</tr>
{/rows}
```

### First/Last Handling

```html
{items}
  {if first@items}<ul>{/if}
  <li>{$name}</li>
  {if last@items}</ul>{/if}
{/items}
```

### Item Separators

```html
{tags}
  {$name}{if notLast@tags}, {/if}
{/tags}
```

### Counter Display

```html
{users}
  <div>
    {current@users} of {count@users}: {$name}
    {if remaining@users}({remaining@users} remaining){/if}
  </div>
{/users}
```

### Dynamic Field Display

```html
{record}
  <dl>
  {fields@record}
    <dt>{$name}</dt>
    <dd>{$value}</dd>
  {/fields}
  </dl>
{/record}
```

### Accessing Parameters

```html
{customTag mode="advanced" debug="true"}
  Mode: {parameter:mode@customTag}
  Debug: {parameter:debug@customTag}
{/customTag}
```
