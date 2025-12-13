# PAD - PHP Application Driver

PAD is a powerful PHP template engine and framework that uses an **inversion of control** approach to web application development. Instead of embedding PHP in HTML, PAD processes template files containing intuitive curly-brace tags, merging them with your PHP logic to produce the final output.

---

## How PAD Works

1. **Your PHP code runs first** - Sets up variables, processes forms, queries databases
2. **PAD reads your template** - Parses `.pad` files containing HTML with `{tags}`
3. **PAD merges and outputs** - Combines data with template and sends to browser

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│   PHP Code  │ --> │  PAD Engine │ --> │   Output    │
│  (logic)    │     │  (template) │     │   (HTML)    │
└─────────────┘     └─────────────┘     └─────────────┘
```

---

## Quick Example

**hello.php** - Your PHP logic:
```php
<?php
$name = 'World';
$items = ['Apple', 'Banana', 'Cherry'];
?>
```

**hello.pad** - Your template:
```html
<h1>Hello, {$name}!</h1>

<ul>
{array $items}
  <li>{$value}</li>
{/array}
</ul>
```

**Output:**
```html
<h1>Hello, World!</h1>

<ul>
  <li>Apple</li>
  <li>Banana</li>
  <li>Cherry</li>
</ul>
```

---

## Key Features

### Variable Expressions
Access PHP variables directly in templates with optional transformations:

```html
{$username}                    <!-- Simple variable -->
{$name|upper}                  <!-- Apply function -->
{$price|number_format(2)}      <!-- Function with arguments -->
{$text|trim|upper|left(10)}    <!-- Chained pipes -->
```

### Control Flow
Full control flow support with familiar syntax:

```html
{if $loggedIn}
  Welcome back, {$username}!
{elseif $remembered}
  Welcome back! Please log in.
{else}
  Please sign in or register.
{/if}

{while $hasMore}
  {$item}
{/while}

{case $status}
  {when 'active'}Active
  {when 'pending'}Pending
  {when 'closed'}Closed
{/case}
```

### Data Iteration
Iterate over arrays, database results, or stored data:

```html
{array $users}
  <div class="user">
    <h3>{$name}</h3>
    <p>{$email}</p>
  </div>
{/array}

{record products where category='electronics' order price}
  <li>{$name} - ${$price}</li>
{/record}
```

### Data Storage
Store data and content for reuse:

```html
{data navigation}
  <nav>...</nav>
{/data}

{content sidebar}
  <aside>...</aside>
{/content}

<!-- Use later -->
{navigation}
{sidebar}
```

### Mathematical Sequences
Built-in support for mathematical number sequences:

```html
{sequence:range from=1 to=10}
  {$value}
{/sequence}

{sequence:primes count=10}
  {$value}  <!-- 2, 3, 5, 7, 11, 13, 17, 19, 23, 29 -->
{/sequence}

{sequence:fibonacci count=8}
  {$value}  <!-- 0, 1, 1, 2, 3, 5, 8, 13 -->
{/sequence}
```

### File Operations
Read and write files, iterate directories:

```html
{files dir=uploads mask=*.jpg}
  <img src="{$path}" alt="{$name}">
{/files}

{file dir=reports name=monthly ext=html}
  {$reportContent}
{/file}
```

### HTTP Requests
Make external HTTP requests:

```html
{curl https://api.example.com/data}
  {$response}
{/curl}
```

---

## Special Prefixes

PAD uses special prefixes to access different types of values:

| Prefix | Meaning | Example |
|--------|---------|---------|
| `$` | Variable/field | `{$username}` |
| `&` | Tag value | `{&tagname}` |
| `#` | Option/parameter | `{#option}` |
| `@` | Property access | `{@count}`, `{$user.name@length}` |
| `?` | URL-encoded value | `{?search}` |
| `!` | Raw value (unescaped) | `{!html}` |

---

## Architecture Overview

```
PAD Framework
├── Entry Point (pad.php)
├── Build (build/) - Template construction
├── Level (level/) - Main processing loop
├── Types (types/) - Tag type handlers
├── Tags (tags/) - Built-in tags
├── Functions (functions/) - Pipe functions
├── Eval (eval/) - Expression evaluation
├── Sequence (sequence/) - Number sequences
├── Lib (lib/) - Core library functions
└── Config (config/) - Configuration
```

### Processing Pipeline

1. **Build** - Constructs template from page files
2. **Occurrence** - Parses template to find tags
3. **Level** - Processes tags level by level (nested)
4. **Type Resolution** - Determines tag type
5. **Tag Execution** - Executes appropriate handler
6. **Output** - Finalizes and sends result

---

## Configuration

PAD requires two constants to be defined before inclusion:

```php
define('APP', '/path/to/your/app/');  // Application root
define('DAT', '/path/to/data/');       // Data directory

require_once '/path/to/pad/pad.php';
```

### Key Settings

```php
$padErrorAction = 'pad';     // Error handling: pad, boot, php, stop, exit, ignore, log, dump
$padOutputType = 'web';      // Output type: web, file, download, console
$padCache = true;            // Enable caching
```

---

## Built-in Tags Summary

| Category | Tags |
|----------|------|
| Control Flow | `if`, `elseif`, `else`, `while`, `until`, `case`, `when`, `switch` |
| Data | `data`, `content`, `array`, `record`, `field`, `bool` |
| Variables | `set`, `increment`, `decrement`, `count` |
| Output | `echo`, `dump`, `tidy`, `output` |
| Files | `file`, `files`, `dir`, `exists` |
| HTTP | `curl`, `ajax`, `redirect`, `page` |
| Flow | `exit`, `restart`, `continue`, `ignore` |
| Errors | `error`, `exception` |
| Constants | `true`, `false`, `null`, `open`, `close` |
| Debug | `trace`, `dump` |

For complete tag documentation, see [tags/TAGS.md](tags/TAGS.md).

---

## Pipe Functions

Transform values using pipe syntax:

```html
{$text|upper}                  <!-- UPPERCASE -->
{$text|lower}                  <!-- lowercase -->
{$text|trim}                   <!-- Remove whitespace -->
{$text|left(5)}                <!-- First 5 chars -->
{$text|right(5)}               <!-- Last 5 chars -->
{$text|replace('a','b')}       <!-- Replace text -->
{$number|number_format(2)}     <!-- Format number -->
{$date|date('Y-m-d')}          <!-- Format date -->
{$html|sanitize}               <!-- Sanitize HTML -->
{$text|default('N/A')}         <!-- Default if empty -->
```

---

## Project Structure

```
your-app/
├── _config/           # App configuration
├── _data/             # Data storage (JSON files)
├── _include/          # Reusable templates
├── _inits.php         # Global PHP initialization
├── _inits.pad         # Global layout template
├── index.php          # Home page logic
├── index.pad          # Home page template
└── pages/
    ├── about.php
    ├── about.pad
    └── ...
```

---

## License

Copyright (c) Herbert Groot

---

# Documentation Index

This file provides an index to all documentation files (`*.md`) in the PAD framework.

---

## Overview

| File | Description |
|------|-------------|
| [CLAUDE.md](CLAUDE.md) | Main project overview, architecture, and code conventions |

---

## Bug Reports

| File | Description |
|------|-------------|
| [lib/BUGS.md](lib/BUGS.md) | Bugs found in core library functions (13 issues) |
| [level/BUGS.md](level/BUGS.md) | Bugs found in main processing loop (9 issues) |

---

## Detailed Explanations

| File | Description |
|------|-------------|
| [eval/EXPLAIN.md](eval/EXPLAIN.md) | Deep dive into the expression evaluation system |
| [sequence/EXPLAIN.md](sequence/EXPLAIN.md) | Comprehensive guide to mathematical sequences |

---

## Tag Reference

| File | Description |
|------|-------------|
| [tags/TAGS.md](tags/TAGS.md) | Reference for all built-in PAD tags |

---

## Module Documentation (CLAUDE.md files)

### Core Processing

| Directory | File | Description |
|-----------|------|-------------|
| `level/` | [CLAUDE.md](level/CLAUDE.md) | Main tag processing loop - heart of the engine |
| `occurrence/` | [CLAUDE.md](occurrence/CLAUDE.md) | Template parsing and tag occurrence detection |
| `build/` | [CLAUDE.md](build/CLAUDE.md) | Template construction from page files |
| `types/` | [CLAUDE.md](types/CLAUDE.md) | Tag type handlers (app, pad, data, function, etc.) |

### Evaluation & Expressions

| Directory | File | Description |
|-----------|------|-------------|
| `eval/` | [CLAUDE.md](eval/CLAUDE.md) | Expression parsing and evaluation |
| `at/` | [CLAUDE.md](at/CLAUDE.md) | Property access via `@` syntax |
| `sequence/` | [CLAUDE.md](sequence/CLAUDE.md) | Mathematical sequence generators |

### Tags & Functions

| Directory | File | Description |
|-----------|------|-------------|
| `tags/` | [CLAUDE.md](tags/CLAUDE.md) | Built-in control flow tags (if, while, array, etc.) |
| `tag/` | [CLAUDE.md](tag/CLAUDE.md) | Tag value resolution |
| `functions/` | [CLAUDE.md](functions/CLAUDE.md) | Pipe functions (trim, upper, replace, etc.) |
| `constructs/` | [CLAUDE.md](constructs/CLAUDE.md) | Language constructs |

### Data & Storage

| Directory | File | Description |
|-----------|------|-------------|
| `data/` | [CLAUDE.md](data/CLAUDE.md) | Data store operations |
| `database/` | [CLAUDE.md](database/CLAUDE.md) | Database connectivity |
| `cache/` | [CLAUDE.md](cache/CLAUDE.md) | Caching system |

### Control Flow

| Directory | File | Description |
|-----------|------|-------------|
| `handling/` | [CLAUDE.md](handling/CLAUDE.md) | Tag handling and flow control |
| `walk/` | [CLAUDE.md](walk/CLAUDE.md) | Data iteration/walking |
| `options/` | [CLAUDE.md](options/CLAUDE.md) | Tag option processing |

### Initialization & Configuration

| Directory | File | Description |
|-----------|------|-------------|
| `start/` | [CLAUDE.md](start/CLAUDE.md) | Application startup |
| `inits/` | [CLAUDE.md](inits/CLAUDE.md) | Initialization routines |
| `config/` | [CLAUDE.md](config/CLAUDE.md) | Configuration settings |
| `install/` | [CLAUDE.md](install/CLAUDE.md) | Installation procedures |

### Error Handling & Exit

| Directory | File | Description |
|-----------|------|-------------|
| `error/` | [CLAUDE.md](error/CLAUDE.md) | Error handling system |
| `exits/` | [CLAUDE.md](exits/CLAUDE.md) | Exit and output finalization |
| `try/` | [CLAUDE.md](try/CLAUDE.md) | Try/catch wrappers |

### Utilities

| Directory | File | Description |
|-----------|------|-------------|
| `lib/` | [CLAUDE.md](lib/CLAUDE.md) | Core library functions |
| `call/` | [CLAUDE.md](call/CLAUDE.md) | Function call utilities |
| `callback/` | [CLAUDE.md](callback/CLAUDE.md) | Callback handling |
| `get/` | [CLAUDE.md](get/CLAUDE.md) | Value retrieval utilities |

### Debugging & Events

| Directory | File | Description |
|-----------|------|-------------|
| `info/` | [CLAUDE.md](info/CLAUDE.md) | Debug info and tracing |
| `events/` | [CLAUDE.md](events/CLAUDE.md) | Event system |

---

## Quick Reference

### Finding Information

| Looking for... | See... |
|----------------|--------|
| How PAD works overall | [CLAUDE.md](CLAUDE.md) |
| How expressions are evaluated | [eval/EXPLAIN.md](eval/EXPLAIN.md) |
| How sequences work | [sequence/EXPLAIN.md](sequence/EXPLAIN.md) |
| List of built-in tags | [tags/TAGS.md](tags/TAGS.md) |
| Known bugs | [lib/BUGS.md](lib/BUGS.md), [level/BUGS.md](level/BUGS.md) |
| Main processing loop | [level/CLAUDE.md](level/CLAUDE.md) |
| Pipe functions | [functions/CLAUDE.md](functions/CLAUDE.md) |

### File Statistics

- **Total documentation files:** 37
- **CLAUDE.md files:** 30 (module documentation)
- **BUGS.md files:** 2 (bug reports)
- **EXPLAIN.md files:** 2 (detailed explanations)
- **Other:** 3 (TAGS.md, INDEX.md, README.md)

---

*Last updated: December 2024*
