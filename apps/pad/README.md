# PAD Reference Application

This application serves a dual purpose: it is both a **working example** of a PAD application and the **official manual** for the PAD framework.

## What is PAD?

PAD (PHP Application Driver) is an Inversion of Control PHP template engine where templates drive application flow. For detailed framework documentation, see [../../README.md](../../README.md).

## Directory Structure

### Content Directories

| Directory | Description |
|-----------|-------------|
| `manual/` | Interactive documentation pages explaining PAD concepts (template syntax, levels, occurrences, data handling, callbacks, properties, options) |
| `reference/` | Cross-reference system that dynamically generates documentation by scanning PAD framework directories |
| `` | Test fragments and working examples organized by category (30+ subdirectories) |
| `regression/` | Automated regression testing system comparing fragment output against stored references |
| `sequence/` | Comprehensive documentation and examples for the Sequence subsystem (80+ sequences, actions, plays) |
| `develop/` | Development and debugging utilities (benchmarking, build tools, manual page rendering) |
| `examples/` | Source examples with .pad/.php/.html triples for documentation |

### Extension Directories

| Directory | Description |
|-----------|-------------|
| `_tags/` | Custom application tags (demo, table, example, fragment, etc.) |
| `_functions/` | Custom pipe functions (bm, foo, substr) |
| `_lib/` | Shared PHP libraries (colors, file handling, menu, reference utilities) |
| `_include/` | Reusable template snippets (menu, blocks, line decorations) |
| `_data/` | Static data files (staff.xml, mondial.xml for examples) |
| `_callbacks/` | Callback handlers (before, demand) |
| `_options/` | Custom tag options (source) |
| `_config/` | Application configuration |
| `_install/` | Database schemas (classicmodels.sql, demo.sql) |

## Content Details

### manual/

Interactive documentation pages, each demonstrating the concepts it describes:

- `index.pad` - Manual home page
- `tags.pad` - Tag syntax and structure
- `levels_and_occurrences.pad` - Understanding PAD's level system
- `level_and_occurrence_variables.pad` - `$` vs `%` variables
- `data_handling.pad` - Working with data arrays
- `callback.pad` - Callback system
- `properties.pad` - Tag properties (`@tag` syntax)
- `options_variables_parameters.pad` - Parameter types
- `the_data_tag.pad` - The `{data}` tag
- `the_content_tag.pad` - The `{content}` tag
- `php_and_html.pad` - PHP/HTML integration
- `escaping_special_PAD_chars.pad` - Escaping `{` and `}`
- `prefix.pad` - Type prefixes (`app:`, `pad:`, `php:`, etc.)
- `sequence/` - Sequence subsystem manual

### reference/

Dynamic cross-reference system with cached data in `DATA/`:

- Browsable indexes of all tags, functions, options, types, properties
- Reads framework source files and presents them in navigable format
- Fragment references stored in `_data/fragments.json`

### 

Test fragments organized by category:

| Category | Contents |
|----------|----------|
| `hello/` | Hello World examples |
| `tags/` | Tag usage examples |
| `functions/` | Pipe function examples |
| `constructs/` | Special constructs (else, start, end) |
| `content/` | Content tag examples |
| `data/` | Data tag examples |
| `db/` | Database integration (array, record, field, check, join, union) |
| `error/` | Error handling examples |
| `lvl_occ/` | Level and occurrence examples |
| `php_and_html/` | PHP/HTML integration examples |
| `properties/` | Property examples |
| `options/` | Option examples |
| `callback/` | Callback examples |
| `classicModels/` | Examples using the classicmodels database |
| `claude/` | Claude-generated examples (tags, functions, properties) |
| `control/` | Flow control (break, continue) |
| `deep/` | Deeply nested tag examples |
| `file/` | File operation examples |
| `miscellaneous/` | Various other examples |
| `prefix/` | Type prefix examples |
| `staff/` | Staff XML examples |
| `start/` | Start/end tag examples |
| `tableFun/` | Table function examples |
| `tag_return_values/` | Tag return value examples |
| `vars/` | Variable examples |
| `walk/` | Walk system examples |

### regression/

Automated testing system:

1. Iterates through all fragments
2. Renders each fragment via HTTP request
3. Compares output against stored `.txt` reference files
4. Reports status: ok, warning, error, new

Reference files stored in `DATA/`.

### sequence/

Comprehensive sequence documentation:

| Subdirectory | Contents |
|--------------|----------|
| `basic/` | Basic sequence type examples (80+ sequences) |
| `concepts/` | Core concepts (actions, plays, resume, stores) |
| `claude/` | Claude-generated sequence examples |
| `keepRemoveFlag/` | Keep/remove/flag play examples |
| `play/single/` | Single-parameter play examples |
| `play/double/` | Double-parameter play examples |
| `random/` | Random sequence examples |
| `regression/` | Sequence regression tests |
| `specials/` | Special sequence combinations |
| `type/` | Sequence type-specific examples |

### develop/

Development utilities:

- `benchmark/` - Performance benchmarking
- `build/` - Build scripts for generating sequence pages
- `buildOld/` - Legacy build scripts
- `all.pad/php` - Generate all pages
- `manual.pad/php` - Manual page rendering
- `site.pad/php` - Site generation

## Entry Points

| File | Description |
|------|-------------|
| `index.pad/php` | Application home page |
| `_inits.pad/php` | Global initialization and layout wrapper |
| `test.pad/php` | Test page |
| `xxx.pad` | Scratch/experimental page |

## Database

The `_install/` directory contains SQL schemas:

- `classicmodels.sql` - Sample database (employees, offices, orders, etc.)
- `demo.sql` - Demo tables
- `database.sql` - Basic database setup

## Running

Access via web browser: `http://server/pad/`

Key entry points:
- `/pad/` - Home page
- `/pad/?manual` - Framework manual
- `/pad/?reference` - Cross-reference system
- `/pad/?regression` - Run regression tests
- `/pad/?sequence` - Sequence documentation
- `/pad/?develop` - Development tools

## Features Demonstrated

- Page pairing (`.php` data + `.pad` template)
- Global wrapper (`_inits.pad` with `?` placeholder)
- Custom tags, functions, options
- Database integration
- File operations
- Callbacks
- Sequences and transformations
- Regression testing
- Cross-reference documentation generation
