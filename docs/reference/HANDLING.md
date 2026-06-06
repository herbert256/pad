# PAD Data Handling Functions Reference

This document provides a complete reference for all PAD data handling functions.

## Overview

Data handling functions transform data arrays before they are rendered in templates. They are applied as options on PAD tags:

```
{tagName sort="field" first="5" reverse}
```

Multiple handlers can be combined and are applied in sequence.

---

## Sorting Functions

### sort

Sorts data by one or more fields with configurable direction and sort type.

```
{data sort}
{data sort="fieldName"}
{data sort="fieldName ASC"}
{data sort="fieldName DESC"}
{data sort="field1 ASC; field2 DESC"}
```

**Parameters:**
- `TRUE` or empty - Sort by all fields
- `fieldName` - Sort by single field (ascending)
- `fieldName ASC` - Sort ascending
- `fieldName DESC` - Sort descending
- Multiple fields separated by `;`

**Sort Type Flags:**
- `NUMERIC` - Compare as numbers
- `STRING` - Compare as strings
- `NATURAL` - Natural order sorting
- `LOCALE_STRING` - Locale-aware string comparison
- `REGULAR` - Standard comparison

**Examples:**
```html
{users sort="name"}
{users sort="age DESC"}
{users sort="department ASC; name ASC"}
{products sort="price NUMERIC DESC"}
{files sort="name NATURAL ASC"}
```

**Implementation:** Uses `array_multisort()` for efficient multi-column sorting.

---

### reverse

Reverses the order of data array.

```
{data reverse}
```

**Parameters:** None (boolean flag)

**Example:**
```html
{users sort="date" reverse}
{-- Sort by date then reverse for newest first --}
```

**Implementation:** Uses `array_reverse()`.

---

### shuffle

Randomly shuffles all elements.

```
{data shuffle}
```

**Parameters:** None (boolean flag)

**Example:**
```html
{quotes shuffle first="1"}
{-- Get one random quote --}
```

**Implementation:** Uses PHP `shuffle()`.

---

### random

Selects random elements with advanced options.

```
{data random}
{data random="5"}
{data random="3" orderly duplicates}
```

**Parameters:**
- `random` - Number of random items to select (default: all)
- `orderly` - Keep original order of selected items
- `duplicates` - Allow duplicate selections

**Examples:**
```html
{products random="4"}
{-- Select 4 random products --}

{users random="3" orderly}
{-- Select 3 random users, maintain their original order --}

{colors random="10" duplicates}
{-- Select 10 colors, allowing repeats --}
```

**Implementation:** Uses `pqRandom()` function.

---

## Selection Functions

### first

Gets the first N elements.

```
{data first}
{data first="5"}
```

**Parameters:**
- `TRUE` or `1` - Get first element
- Number - Get first N elements

**Examples:**
```html
{news first="3"}
{-- Show first 3 news items --}

{users sort="score DESC" first="10"}
{-- Top 10 users by score --}
```

**Implementation:** Uses `array_slice($data, 0, $count)`.

---

### last

Gets the last N elements.

```
{data last}
{data last="5"}
```

**Parameters:**
- `TRUE` or `1` - Get last element
- Number - Get last N elements

**Examples:**
```html
{logs last="20"}
{-- Show last 20 log entries --}

{orders sort="date" last="5"}
{-- Last 5 orders --}
```

**Implementation:** Uses `array_slice($data, -$count)`.

---

### row

Gets a specific row by number.

```
{data row="3"}
```

**Parameters:**
- Row number (1-based index)

**Example:**
```html
{users row="1"}
{-- Get first user --}
```

**Implementation:** Uses `padHandGo()` with start=end.

---

## Pagination Functions

### page

Paginates data into pages.

```
{data page="1" rows="10"}
{data page="2" rows="25"}
```

**Parameters:**
- `page` - Page number (1-based, default: 1)
- `rows` - Items per page (default: 10)

**Calculation:**
- Start = ((page - 1) × rows) + 1
- End = start + rows - 1

**Examples:**
```html
{users page="1" rows="20"}
{-- First 20 users --}

{products page="3" rows="12"}
{-- Products 25-36 (page 3 with 12 per page) --}
```

---

### rows

Gets a specific number of rows (shorthand for pagination).

```
{data rows="10"}
```

**Parameters:**
- Number of rows to retrieve

**Behavior:**
- If `page` is not set, triggers pagination with default page 1
- If `start` is set, limits rows from that position
- If `end` is set, calculates start based on end - rows

**Example:**
```html
{users rows="5"}
{-- First 5 users --}

{logs end="-1" rows="10"}
{-- Last 10 log entries --}
```

---

### start

Sets the starting position for data selection.

```
{data start="5"}
{data start="-3"}
```

**Parameters:**
- Positive number - Start from this position (1-based)
- Negative number - Start from end (-1 = last, -2 = second to last)

**Examples:**
```html
{users start="11" rows="10"}
{-- Users 11-20 --}

{items start="-5"}
{-- Last 5 items --}
```

---

### end

Sets the ending position for data selection.

```
{data end="10"}
{data end="-1"}
```

**Parameters:**
- Positive number - End at this position
- Negative number - End relative to array end

**Examples:**
```html
{users start="1" end="10"}
{-- Users 1-10 --}

{items end="-2"}
{-- All items except last one --}
```

---

## Slicing Functions

### slice

Extracts a portion of the array (keeps selected elements).

```
{data slice="5"}
{data slice="3|7"}
```

**Parameters:**
- Single number - Keep first N elements (positive) or last N (negative)
- `start|count` - Start position and number of elements

**Examples:**
```html
{items slice="10"}
{-- Keep first 10 items --}

{items slice="-5"}
{-- Keep last 5 items --}

{items slice="5|3"}
{-- Keep 3 items starting at position 5 --}
```

**Implementation:** Uses `array_slice()`.

---

### splice

Removes a portion of the array (removes selected elements).

```
{data splice="5"}
{data splice="3|2"}
```

**Parameters:**
- Single number - Remove first N (positive) or last N (negative)
- `start|count` - Start position and number to remove

**Examples:**
```html
{items splice="2"}
{-- Remove first 2 items --}

{items splice="-3"}
{-- Remove last 3 items --}

{items splice="5|2"}
{-- Remove 2 items starting at position 5 --}
```

**Implementation:** Uses `array_splice()`.

---

## Data Cleaning Functions

### dedup

Removes duplicate entries from single-field arrays.

```
{data dedup}
```

**Parameters:** None (boolean flag)

**Behavior:**
- Works on arrays where each element has a single field
- Uses the field value as the deduplication key
- Preserves the field name structure

**Example:**
```html
{tags dedup}
{-- Remove duplicate tags --}
```

---

### trim

Trims elements from the beginning and/or end of the array.

```
{data trim}
{data trim="2"}
{data trim="3" left}
{data trim="3" right}
{data trim="2" both}
```

**Parameters:**
- Number - Elements to trim (default: 1)
- `both` - Trim from both ends (default if no direction specified)
- `left` - Trim from beginning only
- `right` - Trim from end only

**Examples:**
```html
{items trim="1"}
{-- Remove first and last item --}

{items trim="2" left}
{-- Remove first 2 items --}

{items trim="3" right}
{-- Remove last 3 items --}
```

**Implementation:** Uses `pqTruncate()` function.

---

## Negative Mode

The `negative` option inverts the selection - keeping items that would normally be removed.

```
{data first="3" negative}
{-- Gets all items EXCEPT the first 3 --}
```

**How it works:**
1. Keys are prefixed with 'x' before operation
2. Handler runs normally
3. Items that remain are removed from original
4. Original items NOT selected are kept

**Examples:**
```html
{users first="5" negative}
{-- All users except first 5 --}

{items random="3" negative}
{-- All items except 3 random ones --}
```

---

## Function Summary Table

| Function | Purpose | Parameters |
|----------|---------|------------|
| `sort` | Sort by field(s) | field [ASC\|DESC] [; ...] |
| `reverse` | Reverse order | (none) |
| `shuffle` | Random order | (none) |
| `random` | Random selection | count, orderly, duplicates |
| `first` | First N items | count |
| `last` | Last N items | count |
| `row` | Specific row | row number |
| `page` | Pagination | page, rows |
| `rows` | Row count | count |
| `start` | Start position | position |
| `end` | End position | position |
| `slice` | Keep portion | start\|count |
| `splice` | Remove portion | start\|count |
| `dedup` | Remove duplicates | (none) |
| `trim` | Trim ends | count, left, right, both |

---

## Combining Functions

Functions can be combined and are applied in sequence:

```html
{users sort="name" first="10"}
{-- Sort by name, then get first 10 --}

{products sort="price DESC" page="1" rows="20"}
{-- Sort by price descending, paginate --}

{items shuffle first="5"}
{-- Shuffle then get first 5 (5 random items) --}

{logs sort="date DESC" dedup first="100"}
{-- Sort, deduplicate, limit to 100 --}
```

---

## Processing Order

Handlers are processed in the order they appear in the tag parameters. Consider the order carefully:

```html
{-- Different results: --}
{items first="10" shuffle}    {-- First 10, then shuffled --}
{items shuffle first="10"}    {-- Shuffled, then first 10 --}
```

**Recommended order:**
1. `dedup` - Remove duplicates first
2. `sort` - Sort the clean data
3. `shuffle` / `random` - Randomize if needed
4. `first` / `last` / `page` - Limit results last
