# PAD Constructs Reference

This document provides a complete reference for all PAD constructs - special `@something@` markers used in PAD templates.

## Overview

PAD constructs are special placeholder markers that control template structure and content flow. They use the `@name@` syntax and are processed during template building and rendering.

```
@page@
@content@
@start@
@end@
@else@
@tidy@
```

---

## Template Structure Constructs

### @page@

The main content placeholder that marks where page content should be inserted.

**Purpose:** Central insertion point for page content in the template hierarchy.

**Usage:**
```html
<!DOCTYPE html>
<html>
<head><title>My App</title></head>
<body>
  @page@
</body>
</html>
```

**Behavior:**
- Used in `_inits.pad` and `_exits.pad` files to wrap page content
- During build, `@page@` is replaced with the actual page content
- Multiple init/exit files are nested around `@page@`

**Build Process** (see `build/base.php` and `build/build.php`):
1. Starts with `@page@` as the base
2. Each directory's `_inits.pad` and `_exits.pad` wrap around it
3. Final page content replaces `@page@`

**Example with Init/Exit:**
```html
<!-- _inits.pad -->
<html><body>
@page@

<!-- _exits.pad -->
</body></html>

<!-- Result: <html><body> [page content] </body></html> -->
```

---

### @content@

Content merge placeholder for inserting content into parent templates.

**Purpose:** Marks where child content should be merged into parent content.

**Usage:**
```html
<!-- _tags/article.pad - used as {article}<p>child</p>{/article} -->
<article>
  <header>Article Header</header>
  @content@
  <footer>Article Footer</footer>
</article>
```

**Behavior:**
- Used in the content merging system (`lib/content.php`): a custom tag's template, the
  `content=` option, a `{slot}` frame - written in a page with nothing merging into it, the
  strict check stops it ("an @content@ stands where nothing merges content into it")
- Content before `@content@` becomes the prefix
- Content after `@content@` becomes the suffix
- Child content is inserted at the `@content@` position

**Merge Options** (`merge=`, when neither side holds an `@content@`):
- `merge="top"` - the merged content above the tag's own (the default)
- `merge="bottom"` - below it
- `merge="replace"` - instead of it

---

## Processing Control Constructs

### @start@

Start marker: what stands before it is a prelude, rendered once before the rows.

**Purpose:** Splits a tag's content into a prelude, the body that renders per row, and - with
`@end@` - a coda.

**Usage:**
```html
{myTag}
  <header>once, before the rows</header>
  @start@
  <li>per row</li>
  @end@
  <footer>once, after the rows</footer>
{/myTag}
```

**Behavior:**
- Detected by `padOpenCloseOk()` (see `level/start.php`, `level/start_end/`)
- The content before `@start@` renders once, before the first row
- The content between `@start@` and `@end@` renders for each row
- `@start@` and `@end@` go together: the strict check refuses one without the other ("an
  @start@ needs its @end@ behind it") or the two in the wrong order

**Use Cases:**
- A table header above the rows
- Separation of setup and main content

---

### @end@

End marker: what stands after it is a coda, rendered once after all rows.

**Purpose:** Closes the per-row body that `@start@` opened.

**Usage:** see `@start@` above - the two are written together.

**Behavior:**
- Detected by `padOpenCloseOk()` (see `level/start.php`, `level/start_end/`)
- The content after `@end@` renders once, after the last row (`count@` and the other
  properties of the level are there)
- Without its `@start@` the strict check refuses it ("an @end@ needs its @start@ before it")

**Use Cases:**
- Footer content that needs special handling
- Cleanup sections
- Post-iteration content

---

### @else@

True/false branch separator inside a tag's content.

**Purpose:** Splits a tag's content into a "truthy" part and a fallback part - a compact alternative to the `{else}` tag.

**Usage:**
```html
{if $hasEntries}
  {entries}
    <div>{$name}: {$comment}</div>
  {/entries}
@else@
  <p>No entries yet. Be the first to sign!</p>
{/if}
```

**Behavior:**
- Detected in `level/split.php` (and at build time in `build/split.php`)
- Only an `@else@` at the current tag's own nesting level splits the content (markers inside nested open/close tag pairs are ignored)
- Content before `@else@` renders when the tag's condition/data is truthy
- Content after `@else@` renders when it is falsy or empty

---

## Output Control Constructs

### @tidy@

Tidy marker that triggers HTML output formatting.

**Purpose:** Signals that HTML tidying should be applied to the output.

**Usage:**
```html
@tidy@
<html>
<head><title>Page</title></head>
<body>
  <div><p>Content</p></div>
</body>
</html>
```

**Behavior:**
- Presence of `@tidy@` in output triggers HTML tidying
- Checked in `exits/tidy.php`: `strpos($padOutput, '@tidy@')`
- Can also be enabled globally via `$padTidy` variable
- Cleans up whitespace and formats HTML output

**Tidying Effects:**
- Normalizes indentation
- Removes excess whitespace
- Formats HTML tags properly
- Improves output readability

---

## Construct Summary Table

| Construct | Purpose | Replaced With |
|-----------|---------|---------------|
| `@page@` | Main content placeholder | Page content |
| `@content@` | Content merge point | Child content |
| `@start@` | Deferred section start | Split marker |
| `@end@` | Pre-closure section | Split marker |
| `@else@` | True/false branch separator | Split marker |
| `@tidy@` | HTML formatting trigger | Removed (triggers tidy) |

---

## Construct Files

Constructs are registered by files in `PAD/constructs/`:

| File | Construct |
|------|-----------|
| `page.php` | `@page@` |
| `content.php` | `@content@` |
| `start.php` | `@start@` |
| `end.php` | `@end@` |
| `else.php` | `@else@` |
| `tidy.php` | `@tidy@` |

The files are a registry, never included: under the strict check, when the page is built
(`build/build.php`), an `@word@` of the template without its file here is an error -
"there is no @foo@ construct". Text inside `{ignore}` is not read.

---

## Usage Examples

### Page Layout with Init/Exit

**_inits.pad:**
```html
<!DOCTYPE html>
<html>
<head>
  <title>{$pageTitle}</title>
  <link rel="stylesheet" href="/css/style.css">
</head>
<body>
  <nav>{include:navigation}</nav>
  <main>
@page@
```

**_exits.pad:**
```html
  </main>
  <footer>{include:footer}</footer>
</body>
</html>
```

### Content Merging

**Parent template (`_tags/layout.pad`):**
```html
<div class="container">
  <aside class="sidebar">{include:sidebar}</aside>
  <article class="content">
    @content@
  </article>
</div>
```

**Child content, in a page:**
```html
{layout}
  <h1>Page Title</h1>
  <p>Page content goes here...</p>
{/layout}
```

### Empty-Data Fallback

```html
{entries}
  <div>{$name}: {$comment}</div>
@else@
  <p>No entries found.</p>
{/entries}
```

### Two-Phase Processing

```html
{users}
  {-- Header processed first --}
  <table>
    <tr><th>Name</th><th>Email</th></tr>
  @start@
  {-- Rows processed with data --}
    <tr><td>{$name}</td><td>{$email}</td></tr>
  @end@
  {-- Footer processed last --}
  </table>
  <p>Total: {count@users} users</p>
{/users}
```

### Conditional Tidy

```html
{if $debug eq 1}
  @tidy@
{/if}
<html>
  <!-- HTML will be tidied only in debug mode -->
</html>
```

---

## Processing Order

1. **Build Phase:** Init/exit wrapping around `@page@`
2. **Build Phase:** `@page@` replaced with page content
3. **Render Phase:** `@start@`, `@end@` and `@else@` splitting
4. **Render Phase:** `@content@` merging
5. **Exit Phase:** `@tidy@` detection and HTML tidying
