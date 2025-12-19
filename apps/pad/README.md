# PAD Reference Application

This application serves a dual purpose: it is both a **working example** of a PAD application and the **official manual** for the PAD framework.

## What is PAD?

PAD (PHP Application Driver) is an Inversion of Control PHP template engine where templates drive application flow. For detailed framework documentation, see [../../README.md](../../README.md).

## Application Structure

### Manual (`manual/`)

Interactive documentation pages explaining PAD concepts:

- Template syntax and tag structure
- Levels and occurrences
- Data handling and callbacks
- PHP and HTML integration
- Properties and options
- The data and content tags

Each manual page is itself a PAD template, demonstrating the concepts it describes.

### Reference (`reference/`)

Cross-reference system that dynamically generates documentation by scanning the PAD framework directories. Provides browsable indexes of:

- All template tags
- All pipe functions
- All tag options
- All tag types and properties

The reference pages read the framework source files and present them in a navigable format.

### Fragments (`fragments/`)

Test fragments and working examples organized by category:

- `hello/` - Hello World examples
- `tags/` - Tag usage examples
- `functions/` - Pipe function examples
- `constructs/` - Special construct examples
- `db/` - Database integration examples
- `error/` - Error handling examples
- `lvl_occ/` - Level and occurrence examples
- `php_and_html/` - PHP/HTML integration examples

These fragments serve as both documentation and test cases for the regression system.

### Regression (`regression/`)

Automated regression testing system that:

1. Iterates through all fragments
2. Renders each fragment via HTTP request
3. Compares output against stored reference files
4. Reports differences (ok, warning, error, new)

Run regression tests by visiting `/regression` in the browser.

### Sequence (`sequence/`)

Documentation and examples for the Sequence subsystem - a declarative data transformation system within PAD:

- `basic/` - Basic sequence operations (add, subtract, multiply, divide, etc.)
- `concepts/` - Core sequence concepts
- `actions.pad/php` - Available sequence actions
- `examples.pad/php` - Working examples

Sequences allow complex data transformations through chainable operations.

### Develop (`develop/`)

Development and debugging utilities:

- Benchmarking tools
- Build process inspection
- Manual page rendering
- Development scratchpad

## Extension Directories

| Directory | Purpose |
|-----------|---------|
| `_lib/` | Shared PHP libraries |
| `_tags/` | Custom application tags |
| `_functions/` | Custom pipe functions |
| `_callbacks/` | Callback handlers |
| `_options/` | Custom tag options |
| `_include/` | Include templates |
| `_data/` | Application data |
| `_config/` | Configuration files |

## Entry Points

- `index.pad/php` - Application home page
- `_inits.pad/php` - Global initialization (layout wrapper, menu)

## Running

Access via web server at the configured URL. The application demonstrates PAD's automatic file pairing - each `.pad` template is paired with its corresponding `.php` data file.
