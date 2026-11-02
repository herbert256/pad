# PAD Tag Properties Reference

This document provides a complete reference for all PAD tag properties.

## Overview

Tag properties provide contextual information during template execution. They are accessed using the `property@tag` syntax:

```
{property:property@tagname}
```

Where `tagname` is the name of the iteration/loop context and `property` is the information you want to access.

---

## Syntax

### Basic Property Access

```
{property:first@users}
{property:count@items/}
{property:current@rows/}
```

### As a Tag Pair

A boolean property written as a tag pair renders its content only when the property is true:

```
{property:first@users}<ul>{/property:first@users}
{property:last@users}</ul>{/property:last@users}
```

A property that is a data set - `fields`, `keys`, `parameters`, `options`, `variables` - is
iterated as a pair, which is closed by its full name: `{property:fields@user}...{/property:fields@user}`.
Without a target, `{property:fields}...{/property:fields}` reads the enclosing level.

### Inside {pad:if} and {pad:case}

An `{pad:if}` or a `{pad:case}` is no loop of its own: a property written inside one - `{&current}`,
`{property:current@}`, `{property:first}`, `{property:first@}...{/property:first@}` - belongs to the loop around it, as
an option `{#name}` inside one belongs to the tag around it:

```
{array:items}
  {pad:if $price gt 100}<b>{&current}</b>{/pad:if}      the row number of items, not of the if
{/array:items}
```

### In Conditionals

A property reads as a value inside an expression, so it stands in a condition - alone for the
boolean ones, with an operator for the counting ones, and combined freely:

```
{pad:if first@users}<ul>{/pad:if}
{pad:if notFirst@items}, {/pad:if}
{pad:if current@rows eq 2}the second row{/pad:if}
{pad:if first@items or last@items}an end{/pad:if}
{pad:if option.sort@items eq 'name'}sorted by name{/pad:if}
```

The property name is what makes this read as one reference: only the names on this page do
(the files in `pad/properties/`), and the target names an iterating tag. Without a target -
`{pad:if current@ eq 2}`, `{pad:echo current@ * 10/}` - it is the loop the expression stands in, past
an `{pad:if}`, a `{pad:case}` and any other level that renders once for the row - an `{pad:ifchanged}`, a
`{pad:guest}`, a `{pad:spaceless}`, a snippet. Elsewhere in an expression `@` is still the current-value
placeholder, and so is an `@` after a space: `count @`.

**The sigil decides what a colliding name means.** When a row carries a field named like a
property - a `first` column, say - the bare spelling is still the property and the
`$`-spelling is the field:

```
{pad:if first@orders}                  the iteration state - is this the first row
{$first@orders}                    the row's 'first' field
{pad:if $first@orders eq 'Dave'}       comparing that field
```

The tag form resolves fields first, as it always has - the pair
`{property:first@orders}...{/property:first@orders}` over such rows prints the field and renders its content on
every row, and a single `{property:name@users}` over rows with a `name` field prints that field. From inside the level
the `property:` prefix reaches the property whatever the row carries:
`{pad:if property:first}`.

### Ternary Conditionals

```
{property:even@rows ? 'class="even"' : 'class="odd"'}
```

A side that is more than one bare word is quoted: unquoted, `class="even"` is read as an
expression and comes out empty.

---

## Iteration State Properties

Properties that indicate position within a loop iteration.

### first

Returns `TRUE` if this is the first iteration.

```
{property:first@tagname}
```

**Logic:** `$padOccur[$padIdx] == 1`

**Example:**
```html
{array:users}
  {property:first@users}<table><tr><th>Name</th></tr>{/property:first@users}
  <tr><td>{$name}</td></tr>
{/array:users}
```

---

### last

Returns `TRUE` if this is the last iteration.

```
{property:last@tagname}
```

**Logic:** `$padKey[$padIdx] === array_key_last($padData[$padIdx])`

**Example:**
```html
{array:users}
  <tr><td>{$name}</td></tr>
  {property:last@users}</table>{/property:last@users}
{/array:users}
```

---

### notFirst

Returns `TRUE` if this is NOT the first iteration.

```
{property:notFirst@tagname}
```

**Logic:** `! first@tagname`

**Example:**
```html
{array:items}
  {property:notFirst@items}, {/property:notFirst@items}{$name}
{/array:items}
```

**Output:** `item1, item2, item3`

---

### notLast

Returns `TRUE` if this is NOT the last iteration.

```
{property:notLast@tagname}
```

**Logic:** `! last@tagname`

**Example:**
```html
{array:items}
  {$name}{property:notLast@items}|{/property:notLast@items}
{/array:items}
```

**Output:** `item1|item2|item3`

---

### border

Returns `TRUE` if this is either the first OR last iteration.

```
{property:border@tagname}
```

**Logic:** `first@tagname OR last@tagname`

**Example:**
```html
{array:items}
  <div {property:border@items}class="highlight"{/property:border@items}>{$name}</div>
{/array:items}
```

---

### middle

Returns `TRUE` if this is neither first nor last (middle items only).

```
{property:middle@tagname}
```

**Logic:** `NOT border@tagname` (neither first nor last)

**Example:**
```html
{array:items}
  {property:middle@items}<div class="middle">{$name}</div>{/property:middle@items}
{/array:items}
```

---

### even

Returns `TRUE` if the current occurrence number is even.

```
{property:even@tagname}
```

**Logic:** `$padOccur[$padIdx] % 2 == 0`

**Example:**
```html
{array:rows}
  <tr class="{property:even@rows ? even : odd}">{$data}</tr>
{/array:rows}
```

---

### odd

Returns `TRUE` if the current occurrence number is odd.

```
{property:odd@tagname}
```

**Logic:** `NOT even@tagname`

**Example:**
```html
{array:rows}
  <tr class="{property:odd@rows ? odd : even}">{$data}</tr>
{/array:rows}
```

---

## Counter Properties

Properties that provide numeric information about the iteration.

### current

Returns the current occurrence number (1-based index).

```
{property:current@tagname/}
```

**Returns:** `$padOccur[$padIdx]`

**Example:**
```html
{array:users}
  {property:current@users/}. {$name}
{/array:users}
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
{property:count@tagname/}
```

**Returns:** `max(count($padData[$padIdx]), $padOccur[$padIdx])`

**Example:**
```html
{array:users}
  User {property:current@users/} of {property:count@users/}
{/array:users}
```

---

### remaining

Returns the number of iterations remaining.

```
{property:remaining@tagname/}
```

**Returns:** `count($padData[$padIdx]) - $padOccur[$padIdx]` (minimum 0)

**Example:**
```html
{array:users}
  {$name} ({property:remaining@users/} more to go)
{/array:users}
```

---

### done

Returns the number of completed iterations (current - 1).

```
{property:done@tagname}
```

**Returns:** `$padOccur[$padIdx] - 1`

**Example:**
```html
{array:users}
  Processed: {property:done@users}, Current: {$name}
{/array:users}
```

---

## Data Access Properties

Properties that provide access to the current data context.

### data

Returns the complete data array for the current level.

```
{property:data@tagname/}
```

**Returns:** `$padData[$padIdx]`

**Example:**
```html
{array:users}
  {property:data@users toData="allUsers"/}
{/array:users}
```

---

### key

Returns the current array key.

```
{property:key@tagname/}
```

**Returns:** `$padKey[$padIdx]`

**Example:**
```html
{config}
  {property:key@config/}: {$value}
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
{property:keys@tagname/}
```

**Returns:** Array where each item has `name` (the key) and `value`

**Example:**
```html
{array:myArray}
  {property:first@myArray}
    {property:keys@myArray}Key: {$name} {/property:keys@myArray}
  {/property:first@myArray}
{/array:myArray}
```

---

### fields

Returns an array of field name/value pairs from the current record.

```
{property:fields@tagname/}
```

**Returns:** Array where each item has `name` and `value`

**Example:**
```html
{array:user}
  <table>
  {property:fields@user}
    <tr><td>{$name}</td><td>{$value}</td></tr>
  {/property:fields@user}
  </table>
{/array:user}
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
{property:firstFieldName@tagname/}
```

**Returns:** First key from `$padCurrent[$padIdx]`

**Example:**
```html
{array:user}
  Primary field: {property:firstFieldName@user/}
{/array:user}
```

---

### firstFieldValue

Returns the value of the first field in the current record.

```
{property:firstFieldValue@tagname/}
```

**Returns:** First value from `$padCurrent[$padIdx]`

**Example:**
```html
{array:user}
  Primary value: {property:firstFieldValue@user/}
{/array:user}
```

---

## Tag Metadata Properties

Properties that provide information about the current tag.

### name

Returns the name of the current tag/level.

```
{property:name@tagname/}
```

**Returns:** `$padName[$padIdx]`

**Example:**
```html
{array:users}
  Processing tag: {pad:echo name@users/}
{/array:users}
```

**Output:** `Processing tag: users` - written as a tag, `{property:name@users}`, a `name` field of the rows
would win

---

### depth

How deep the level stands in a recursion: for a `{pad:tree}`, 1 for its own rows and one more
per `{pad:recurse}` - for indenting a tree. Any other level counts the levels of its own name
from the top down to itself, so a tag that calls itself has a depth too, and a level that
does not is at depth 1.

```
{property:depth@tree/}
```

**Example:**
```html
{pad:tree 'menu', children='items'}
  <p style="margin-left: {pad:echo depth@tree | * 20/}px">{$title}</p>
  {pad:branch}{pad:recurse/}{/pad:branch}
{/pad:tree}
```

---

## Parameter & Option Properties

Properties for accessing tag parameters and options.

### parameter

Returns one positional parameter - the values after the tag name - numbered from 1.

```
{property:parameter.1@tagname}
```

**Returns:** `$padOpt[$padIdx][$parm]` or `NULL`

**Example:**
```html
{app:myTag 'first', 'second'}
  First: {property:parameter.1@myTag}
  Second: {property:parameter.2@myTag}
{/app:myTag}
```

---

### parameters

Returns all positional parameters as an iterable array.

```
{property:parameters@tagname/}
```

**Returns:** All parameters from `$padOpt[$padIdx]` (excluding index 0, the raw tag text)

**Example:**
```html
{app:myTag 'first', 'second'}
  {property:parameters@myTag}
    Parameter: {$value}
  {/property:parameters@myTag}
{/app:myTag}
```

---

### option

Returns one named option.

```
{property:option.sort@tagname}
```

**Returns:** `$padPrm[$padIdx][$parm]` or `NULL`

**Example:**
```html
{app:myTag sort='name', limit=10}
  Sort by: {property:option.sort@myTag}
  Limit: {property:option.limit@myTag}
{/app:myTag}
```

---

### options

Returns all named options as an iterable array.

```
{property:options@tagname/}
```

**Returns:** All options from `$padPrm[$padIdx]`

**Example:**
```html
{app:myTag foo=1, bar=2}
  {property:options@myTag}
    {$name} = {$value}
  {/property:options@myTag}
{/app:myTag}
```

---

## Variable Properties

Properties for accessing level-scoped variables.

### variable

Returns one level variable - a `$name = value` written on the tag.

```
{property:variable.varName@tagname}
```

**Returns:** `$padSetLvl[$padIdx][$parm]` or `NULL`

**Example:**
```html
{array:users $myVar = 'test'}
  Variable: {property:variable.myVar@users}
{/array:users}
```

---

### variables

Returns all level variables as an iterable array.

```
{property:variables@tagname/}
```

**Returns:** All variables from `$padSetLvl[$padIdx]`

**Example:**
```html
{array:users $a = 1, $b = 2}
  {property:variables@users}
    {$name}: {$value}
  {/property:variables@users}
{/array:users}
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
| `depth@tag` | Number | Depth in a recursion - a `{pad:tree}`'s rows at 1 |
| `parameter.n@tag` | Mixed | Positional parameter n, numbered from 1 |
| `parameters@tag` | Array | All parameters |
| `option.name@tag` | Mixed | The named option's value |
| `options@tag` | Array | All options |
| `variable.x@tag` | Mixed | Level variable |
| `variables@tag` | Array | All variables |

---

## Common Usage Patterns

### Zebra Striping

```html
{array:rows}
  <tr class="{property:even@rows ? row-even : row-odd}">{$data}</tr>
{/array:rows}
```

### First/Last Handling

```html
{array:items}
  {property:first@items}<ul>{/property:first@items}
  <li>{$name}</li>
  {property:last@items}</ul>{/property:last@items}
{/array:items}
```

### Item Separators

```html
{tags}
  {$name}{property:notLast@tags}, {/property:notLast@tags}
{/tags}
```

### Counter Display

```html
{array:users}
  <div>
    {property:current@users/} of {property:count@users/}: {$name}
    {property:notLast@users}({property:remaining@users/} remaining){/property:notLast@users}
  </div>
{/array:users}
```

### Dynamic Field Display

```html
{pad:record "* from users where id=5"}
  <dl>
  {property:fields@record}
    <dt>{$name}</dt>
    <dd>{$value}</dd>
  {/property:fields@record}
  </dl>
{/pad:record}
```

### Accessing Parameters

```html
{app:customTag 'fast', mode='advanced'}
  Speed: {property:parameter.1@customTag}
  Mode: {property:option.mode@customTag}
{/app:customTag}
```
