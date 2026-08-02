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
<!-- Parent template -->
<article>
  <header>Article Header</header>
  @content@
  <footer>Article Footer</footer>
</article>
```

**Behavior:**
- Used in the content merging system (`lib/content.php`)
- Content before `@content@` becomes the prefix
- Content after `@content@` becomes the suffix
- Child content is inserted at the `@content@` position

**Merge Options:**
- `merge="top"` - Insert at top of content area
- `merge="bottom"` - Insert at bottom of content area
- Default merges at the `@content@` marker position

---

## Processing Control Constructs

### @start@

Start marker that splits content for deferred processing.

**Purpose:** Marks the beginning of a section that should be processed after initial content.

**Usage:**
```html
{myTag}
  <header>Always shown</header>
  @start@
  <main>Shown after data processing</main>
{/myTag}
```

**Behavior:**
- Detected by `padOpenCloseOk()` (see `level/start.php`)
- Splits `$padBase` into two parts at the `@start@` marker
- Content before `@start@` is processed immediately
- Content after `@start@` is stored in `$padStartBase` for later processing
- Enables two-phase processing within a single tag

**Use Cases:**
- Deferred content rendering
- Conditional section processing
- Separation of setup and main content

---

### @end@

End marker that splits content for pre-processing.

**Purpose:** Marks content that should be processed before the main content ends.

**Usage:**
```html
{myTag}
  <main>Main content</main>
  @end@
  <footer>Processed before tag closes</footer>
{/myTag}
```

**Behavior:**
- Detected by `padOpenCloseOk()` (see `level/start.php`)
- Splits `$padBase` at the `@end@` marker
- Content before `@end@` is the main content
- Content after `@end@` is stored in `$padEndBase`
- Enables pre-closure processing within a single tag

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

Constructs are registered by validation files in `PAD/constructs/`:

| File | Construct | Purpose |
|------|-----------|---------|
| `page.php` | `@page@` | Validates page construct |
| `content.php` | `@content@` | Validates content construct |
| `start.php` | `@start@` | Validates start construct |
| `end.php` | `@end@` | Validates end construct |
| `tidy.php` | `@tidy@` | Validates tidy construct |

All validation files return `TRUE` to indicate the construct is recognized and valid. (`@else@` is handled directly by the split logic in `level/split.php` and `build/split.php` and has no validation file.)

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

**Parent template (layout.pad):**
```html
<div class="container">
  <aside class="sidebar">{include:sidebar}</aside>
  <article class="content">
    @content@
  </article>
</div>
```

**Child content:**
```html
<h1>Page Title</h1>
<p>Page content goes here...</p>
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
