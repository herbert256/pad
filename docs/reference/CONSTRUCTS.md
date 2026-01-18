# PAD Constructs Reference

This document provides a complete reference for all PAD constructs - special `@something@` markers used in PAD templates.

## Overview

PAD constructs are special placeholder markers that control template structure and content flow. They use the `@name@` syntax and are processed during template building and rendering.

```
@pad@
@content@
@start@
@end@
@self@
@page@
@tidy@
```

---

## Template Structure Constructs

### @pad@

The main content placeholder that marks where page content should be inserted.

**Purpose:** Central insertion point for page content in the template hierarchy.

**Usage:**
```html
<!DOCTYPE html>
<html>
<head><title>My App</title></head>
<body>
  @pad@
</body>
</html>
```

**Behavior:**
- Used in `_inits.pad` and `_exits.pad` files to wrap page content
- During build, `@pad@` is replaced with the actual page content
- Multiple init/exit files are nested around `@pad@`

**Build Process:**
1. Starts with `@pad@` as the base
2. Each directory's `_inits.pad` and `_exits.pad` wrap around it
3. Final page content replaces `@pad@`

**Example with Init/Exit:**
```html
<!-- _inits.pad -->
<html><body>
@pad@

<!-- _exits.pad -->
</body></html>

<!-- Result: <html><body> [page content] </body></html> -->
```

---

### @page@

Page-level placeholder, converted to `@pad@` during build.

**Purpose:** Alternative marker for page content, normalized to `@pad@`.

**Usage:**
```html
<div class="page-wrapper">
  @page@
</div>
```

**Behavior:**
- Automatically replaced with `@pad@` during the build process
- Allows templates to use a more semantic name
- Functionally equivalent to `@pad@` after conversion

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
- Used in the content merging system
- `padContentBeforeAfter()` finds `@content@` to split templates
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
- Detected by `padOpenCloseOk()` function
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
- Detected by `padOpenCloseOk()` function
- Splits `$padBase` at the `@end@` marker
- Content before `@end@` is the main content
- Content after `@end@` is stored in `$padEndBase`
- Enables pre-closure processing within a single tag

**Use Cases:**
- Footer content that needs special handling
- Cleanup sections
- Post-iteration content

---

### @self@

Self-referential marker that inserts the current page path.

**Purpose:** Provides a way to reference the current page within templates.

**Usage:**
```html
<form action="@self@" method="post">
  <!-- Form submits to current page -->
</form>

<a href="@self@?refresh=1">Refresh</a>
```

**Behavior:**
- Replaced with `$padGo . $padPage` (current page path)
- Useful for self-referencing forms and links
- Maintains current page context in URLs

**Example Replacement:**
```
@self@ → /myapp/users/edit
```

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
| `@pad@` | Main content placeholder | Page content |
| `@page@` | Page placeholder (alias) | Converted to `@pad@` |
| `@content@` | Content merge point | Child content |
| `@start@` | Deferred section start | Split marker |
| `@end@` | Pre-closure section | Split marker |
| `@self@` | Current page reference | Page path |
| `@tidy@` | HTML formatting trigger | Removed (triggers tidy) |

---

## Construct Files

Each construct has a validation file in `PAD/constructs/`:

| File | Construct | Purpose |
|------|-----------|---------|
| `pad.php` | `@pad@` | Validates pad construct |
| `page.php` | `@page@` | Validates page construct |
| `content.php` | `@content@` | Validates content construct |
| `start.php` | `@start@` | Validates start construct |
| `end.php` | `@end@` | Validates end construct |
| `self.php` | `@self@` | Validates self construct |
| `tidy.php` | `@tidy@` | Validates tidy construct |

All validation files return `TRUE` to indicate the construct is recognized and valid.

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
@pad@
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

### Self-Referencing Form

```html
<form action="@self@" method="post">
  <input type="text" name="search" value="{$search}">
  <button type="submit">Search</button>
</form>
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
  <p>Total: {@count} users</p>
{/users}
```

### Conditional Tidy

```html
{if $debug}
  @tidy@
{/if}
<html>
  <!-- HTML will be tidied only in debug mode -->
</html>
```

---

## Processing Order

1. **Build Phase:** `@page@` → `@pad@` conversion
2. **Build Phase:** Init/exit wrapping around `@pad@`
3. **Build Phase:** `@pad@` replaced with page content
4. **Render Phase:** `@start@` and `@end@` splitting
5. **Render Phase:** `@content@` merging
6. **Render Phase:** `@self@` replacement
7. **Exit Phase:** `@tidy@` detection and HTML tidying
