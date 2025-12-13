# PAD Application

This PAD application serves a dual purpose: it is both a **reference implementation** demonstrating how to build applications with the PAD framework, and the **official manual and documentation** for the PAD framework itself.

## What is PAD?

**PAD** stands for **P**HP **A**pplication **D**river. It is an [Inversion of Control](https://en.wikipedia.org/wiki/Inversion_of_control) (IoC) PHP framework that separates application logic from presentation.

### The PAD Principle

PAD follows the principle: *"Don't call us, we'll call you."*

Unlike traditional PHP frameworks where your code calls framework functions, PAD inverts this relationship:

1. **First**, PAD executes your application PHP code
2. **Then**, PAD reads your HTML/PAD template markup
3. **Finally**, PAD merges both and sends the result to the browser

**There is no PAD code in your application PHP code at all!**

### Hello World Example

A minimal PAD application consists of two files:

**hello.php** (application logic):
```php
<?php
  $greeting = 'Hello World';
?>
```

**hello.pad** (template markup):
```html
<html>
  <body>
    <h1>{$greeting}</h1>
  </body>
</html>
```

The framework automatically merges the PHP variable `$greeting` into the template.

---

## PAD Framework Overview

### Template Syntax

PAD templates support three core syntaxes:

| Syntax | Purpose | Example |
|--------|---------|---------|
| Block tags | Control structures and data iteration | `{tagname param=value}...{/tagname}` |
| Variables | Variable substitution with filters | `{$varname \| filter1 \| filter2}` |
| Properties | Context-aware properties | `{@property}` |

### Built-in Features

- **50+ functions** - Control flow (`if`, `while`, `switch`), data manipulation (`set`, `get`, `array`), utilities (`trace`, `dump`, `redirect`)
- **38 tag filters** - String manipulation (`trim`, `lower`, `replace`), formatting (`date`, `timestamp`), HTML (`sanitize`, `html`, `nbsp`)
- **25 type handlers** - Data sources (`data`, `table`, `field`), includes (`include`, `pad`), code execution (`php`, `script`)
- **Multiple data formats** - CSV, JSON, YAML, XML support
- **Database integration** - Session tracking, request logging, caching
- **Event system** - 60+ event handlers for lifecycle hooks

### Framework Architecture

```
pad/
├── inits/        # Framework initialization (24 modules)
├── start/        # Execution lifecycle management
├── level/        # Scope and nesting management
├── functions/    # Built-in PAD functions
├── tags/         # Template filters
├── types/        # Data type handlers
├── eval/         # Expression parser and evaluator
├── events/       # Event handling system
├── data/         # Data format handlers
├── cache/        # Caching system (memory/database)
├── config/       # Configuration management
├── exits/        # Shutdown and output processing
└── lib/          # Core library utilities
```

---

## This Application's Structure

This application (`apps/pad/`) demonstrates PAD's capabilities while serving as the framework documentation.

### Directory Layout

```
apps/pad/
├── index.php          # Entry point (sets page title)
├── index.pad          # Home page template
├── _inits.php         # Application initialization (PHP)
├── _inits.pad         # Application template wrapper
│
├── manual/            # Framework documentation
├── reference/         # API reference
├── fragments/         # Code examples and demos
├── regression/        # Regression tests
├── sequence/          # Page flow examples
├── develop/           # Development and testing area
│
├── _callbacks/        # Custom callback handlers
├── _config/           # Application configuration
├── _data/             # Application data files
├── _functions/        # Custom PAD functions
├── _include/          # Reusable includes
├── _install/          # Installation scripts
├── _lib/              # Application libraries
├── _options/          # Option handlers
└── _tags/             # Custom tag definitions
```

### Key Files

| File | Purpose |
|------|---------|
| `index.php` | Sets PHP variables (like `$title`) before template processing |
| `index.pad` | Home page content explaining PAD's concept |
| `_inits.php` | Runs before every page; sets up common variables |
| `_inits.pad` | HTML wrapper template; defines page structure with header, menu, content area, footer |

### Documentation Sections

#### Manual (`manual/`)

Comprehensive guides covering PAD concepts:

- `tags.pad` - Tag syntax and usage
- `properties.pad` - Property accessors (`@property`)
- `the_data_tag.pad` - Working with data sources
- `the_content_tag.pad` - Content management
- `levels_and_occurrences.pad` - Scope and iteration
- `level_and_occurrence_variables.pad` - Level variables
- `options_variables_parameters.pad` - Configuration
- `data_handling.pad` - Data format handling
- `callback.pad` - Callback system
- `else.pad` - Conditional logic
- `prefix.pad` - Prefix handling
- `walking.pad` - Tree/structure traversal
- `escaping_special_PAD_chars.pad` - Special character handling
- `php_and_html.pad` - PHP and HTML integration
- `3_ways_to_make_a_table.pad` - Table generation patterns
- `table_fun.pad` - Advanced table techniques
- `tag_return_values.pad` - Return value handling
- `classicModels.pad` - Database example (ClassicModels)

#### Reference (`reference/`)

API reference with cross-referenced documentation of all PAD functions, tags, and types.

#### Fragments (`fragments/`)

Working code examples organized by topic:

- `hello/` - Hello World examples
- `tags/` - Tag usage examples
- `functions/` - Function demonstrations
- `properties/` - Property accessor examples
- `data/` - Data handling examples
- `content/` - Content tag examples
- `constructs/` - Control structure examples
- `db/` - Database integration examples
- `classicModels/` - SQL database examples
- `error/` - Error handling examples
- `callback/` - Callback examples
- `vars/` - Variable handling
- `options/` - Options examples
- `prefix/` - Prefix examples
- `walk/` - Tree walking examples
- `lvl_occ/` - Level and occurrence examples
- `tableFun/` - Table manipulation
- `deep/` - Deep nesting examples
- `miscellaneous/` - Various examples
- `_data/` - Sample data files (XML, JSON, YAML, CSV)
- `_scripts/` - External script examples (Python, Perl, Shell, Groovy)
- `_tags/` - Custom tag examples

---

## Application Conventions

### Directory Prefixes

| Prefix | Meaning |
|--------|---------|
| `_` (underscore) | Special PAD directories processed by the framework |
| No prefix | Regular application directories |

### Special `_` Directories

- `_callbacks/` - PHP files called at specific lifecycle points (`before.php`, `demand.php`)
- `_config/` - Application-specific configuration overrides
- `_data/` - Data files accessible via the `data` type
- `_functions/` - Custom PAD functions (PHP files)
- `_include/` - Reusable PAD includes
- `_install/` - One-time installation scripts
- `_lib/` - PHP library files
- `_options/` - Option processors
- `_tags/` - Custom tag definitions (can be `.pad` or `.php`)

### File Pairs

PAD pages typically consist of paired files:

- `page.php` - PHP logic (executed first)
- `page.pad` - Template markup (processed second)

The PHP file sets variables; the PAD file uses them in the template.

---

## Configuration

This application's configuration is in `_config/`. It can override framework defaults for:

- Error handling behavior
- Cache settings
- Database connections
- Output formatting
- Session management

---

## Running the Application

The application is accessed through the web server via `www/index.php`, which:

1. Detects the platform (Linux/macOS/Windows)
2. Sets the `APP` constant to this application's path
3. Sets the `DAT` constant to the data directory
4. Includes the PAD framework (`pad/pad.php`)

Access the manual at: `http://your-server/pad/manual/`

---

## Copyright

(c) 2003-2025 Herbert Groot Jebbink
