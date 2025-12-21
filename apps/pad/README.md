# PAD Reference Application

This application serves a triple purpose:
1. **Official PAD Framework Manual** - Interactive documentation with live examples
2. **Comprehensive Test Suite** - Regression testing and validation system
3. **Working Reference Implementation** - Demonstrates PAD framework capabilities

## What is PAD?

PAD (PHP Application Driver) is an Inversion of Control PHP template engine where templates drive application flow rather than being included by PHP code. The template structure mirrors the application flow—no routing, no controllers, just create files.

For detailed framework documentation, see [../../README.md](../../README.md) and [../../CLAUDE.md](../../CLAUDE.md).

---

## Directory Structure

### Content Directories (Test Fragments)

PAD includes **32 test fragment categories** at the root level, each demonstrating specific features:

| Directory | Contents | Count |
|-----------|----------|-------|
| `hello/` | Hello World examples | Basic |
| `tags/` | Tag usage examples | 50+ |
| `functions/` | Pipe function examples | 40+ |
| `constructs/` | Special constructs (else, start, end) | Core |
| `content/` | `{content}` tag examples | 7 |
| `data/` | `{data}` tag examples | 3 |
| `db/` | Database integration (array, record, field, check) | 20+ |
| `select/` | PAD Select subsystem (tables, joins, unions) | 6 |
| `error/` | Error handling examples | 3 |
| `lvl_occ/` | Level and occurrence variables | 12 |
| `php_and_html/` | PHP/HTML integration | 12 |
| `properties/` | Property examples (`@` syntax) | 4 |
| `options/` | Tag option examples | 3 |
| `callback/` | Callback system | 2 |
| `control/` | Flow control (break, continue, cease) | 3 |
| `deep/` | Deeply nested tags | 5 |
| `file/` | File operations | 3 |
| `miscellaneous/` | Various examples | 20+ |
| `prefix/` | Type prefix examples (app:, pad:, php:) | 2 |
| `staff/` | Staff XML examples | Multiple |
| `start/` | Start/end tag examples | Multiple |
| `tableFun/` | Table function examples | Multiple |
| `tag_return_values/` | Tag return values | 2 |
| `vars/` | Variable examples | 10+ |
| `walk/` | Walk system examples | 3 |
| `sequence/` | 80+ mathematical sequences | 600+ |

**Total:** 557 example template files demonstrating every framework feature.

### Documentation Directories

| Directory | Description |
|-----------|-------------|
| `manual/` | Interactive documentation pages (20+ topics) |
| `reference/` | Cross-reference system (dynamically scans framework) |
| `regression/` | Automated testing system |
| `examples/` | Generated example outputs (DATA/) |
| `develop/` | Development and build utilities |

### Extension Directories

| Directory | Description | Files |
|-----------|-------------|-------|
| `_tags/` | Custom application tags | 40+ |
| `_functions/` | Custom pipe functions | 3 |
| `_lib/` | Shared PHP libraries | 8 |
| `_include/` | Reusable template snippets | Multiple |
| `_data/` | Static data files (XML, JSON) | 12 |
| `_callbacks/` | Callback handlers | 2 |
| `_options/` | Custom tag options | 1 |
| `_config/` | Application configuration | 1 |
| `_install/` | Database schemas | 3 |
| `_scripts/` | Shell scripts | Multiple |

---

## Manual Pages

The `manual/` directory contains interactive documentation where each page demonstrates the concepts it describes:

### Core Concepts
- `index.pad` - Manual home page and introduction
- `tags.pad` - Tag syntax and structure
- `levels_and_occurrences.pad` - Understanding PAD's level system
- `level_and_occurrence_variables.pad` - `$` vs `%` variables
- `data_handling.pad` - Working with data arrays

### Template Features
- `the_data_tag.pad` - The `{data}` tag for defining data
- `the_content_tag.pad` - The `{content}` tag for storing content
- `callback.pad` - Callback system for data processing
- `properties.pad` - Tag properties (`@tag` syntax)
- `options_variables_parameters.pad` - Parameter types

### Integration
- `php_and_html.pad` - PHP/HTML integration patterns
- `escaping_special_PAD_chars.pad` - Escaping `{` and `}`
- `prefix.pad` - Type prefixes (`app:`, `pad:`, `php:`, etc.)
- `classicModels.pad` - Working with the classicmodels database
- `3_ways_to_make_a_table.pad` - Table generation techniques

### Advanced Topics
- `parse.pad` - Expression parsing
- `table_fun.pad` - Table functions and patterns
- `else.pad` - Else construct usage
- `name.pad` - Name handling
- `examples_used_in_this_manual.pad` - Example index

### Sequence Subsystem
- `manual/sequence/` - Complete sequence documentation
  - `index.pad` - Sequence introduction
  - `examples.pad` - Live sequence examples
  - Organized by type, action, and play

---

## Reference System

The `reference/` directory implements a dynamic cross-reference system:

### Features
- **Auto-discovery**: Scans PAD framework directories to build indexes
- **Live updates**: Regenerates documentation from source code
- **Cross-references**: Links between tags, functions, options, types, properties
- **Example integration**: Shows usage from test fragments

### Reference Data (stored in `DATA/`)
- `tag/` - All tag types (pad, app, data, select, sequence, etc.)
- `functions/` - All pipe functions
- `options/` - All tag options
- `properties/` - All iteration properties (`@` syntax)
- `constructs/` - Special constructs (content, else)
- `sequence/` - Sequence types, actions, plays, builds
- `at/` - Property accessor system

### Data Source
- `_data/xref.json` - Cross-reference metadata

### Entry Points
- `index.pad` - Reference home and search
- `dir.pad` - Directory browser
- `pages.pad` - Page listing by category

---

## Regression Testing System

The `regression/` directory provides automated validation:

### How It Works
1. **Discovery**: Iterates through all test fragments
2. **Execution**: Renders each fragment via HTTP request with `?padInclude&padReference`
3. **Comparison**: Compares output against stored reference files in `DATA/`
4. **Reporting**: Categorizes results as:
   - **ok** - Output matches reference
   - **warning** - Output differs from reference
   - **error** - HTTP error or execution failure
   - **new** - No reference file exists
   - **random** - Contains random values (expected to differ)
   - **empty** - No output generated

### Features
- **Sequence filtering**: Toggle between general tests and sequence-only tests via `?sequence` parameter
- **Visual status**: Color-coded results (green=ok, yellow=warning, red=error, pink=empty)
- **Detailed comparison**: Click any test to see side-by-side comparison
- **Auto-generation**: Creates new reference files for new tests

### Reference Files
- Stored in `DATA/` with `.txt` extension
- One file per test fragment path (e.g., `hello/hello.txt`)
- Contains expected HTML output

### Test Runner
- `index.pad/php` - Main test interface
- `show/` - Detailed comparison view
- `ok.php` - Mark test as accepted
- `_inits.php` - Test initialization and filtering

---

## Examples System

The `examples/` directory contains generated example outputs:

### Purpose
Extracts reusable examples from test fragments for documentation and demos.

### Structure
```
examples/
└── DATA/
    ├── callback/         # Callback examples
    ├── constructs/       # Construct examples
    ├── content/          # Content tag examples
    ├── control/          # Control flow examples
    ├── data/             # Data tag examples
    ├── db/               # Database examples
    ├── error/            # Error handling
    ├── functions/        # Function examples
    ├── hello/            # Hello World
    ├── lvl_occ/          # Level/occurrence vars
    ├── manual/           # Manual examples
    ├── miscellaneous/    # Misc examples
    ├── options/          # Option examples
    ├── php_and_html/     # PHP/HTML integration
    ├── prefix/           # Prefix examples
    ├── properties/       # Property examples
    ├── reference/        # Reference examples
    ├── select/           # PAD Select examples
    ├── sequence/         # Sequence examples
    ├── tag_return_values/# Return value examples
    ├── vars/             # Variable examples
    └── walk/             # Walk system examples
```

### File Format
Each example consists of a triple:
- `.pad` - Template source
- `.php` - Data source (if needed)
- `.html` - Rendered output

### Naming Convention
Examples use flattened naming: `category_subcategory_number.ext`
- Example: `sequence_basic_fibonacci_1.pad`

### Generation
Examples are auto-generated during regression testing from fragments that:
- Execute successfully
- Don't use `{page}`, `{example}`, `{table}`, `{demo}`, or `{ajax}` tags
- Don't contain random/variable output

---

## Sequence Documentation

The `sequence/` directory provides comprehensive sequence subsystem documentation:

### Categories

| Directory | Contents | Count |
|-----------|----------|-------|
| `basic/` | Basic sequence examples (all 80+ types) | 80+ |
| `concepts/` | Core concepts (actions, plays, resume, stores) | 5 |
| `keepRemoveFlag/` | Keep/remove/flag play examples | 80+ |
| `play/single/` | Single-parameter plays | 80+ |
| `play/double/` | Double-parameter plays | 40+ |
| `random/` | Random sequence examples and patterns | 12 |
| `regression/` | Sequence-specific regression tests | Multiple |
| `specials/` | Special combinations (dedup, min/max, reduce) | 10+ |
| `type/` | Type-specific examples | 80+ |

### Sequence Types Demonstrated
- **Mathematical**: fibonacci, lucas, pell, tribonacci, catalan, bell, perrin
- **Prime-related**: prime, composite, perfect, mersenne, emirp, semiprime
- **Figurate**: triangular, square, cubic, pentagonal, hexagonal, tetrahedral
- **Filters**: even, odd, happy, lucky, harshad, palindrome, powerful
- **Generators**: range, list, loop, random, repeat, oeis

### Actions Demonstrated
- **Order**: reverse, sort, shuffle
- **Selection**: first, last, shift, pop, element, slice
- **Aggregation**: sum, product, average, median, minimum, maximum, count
- **Set Operations**: dedup, append, prepend, merge, intersection, difference
- **Transformation**: eval (apply expression to elements)

### Subdirectories
- `_include/` - Shared sequence templates (links, menus)
- `_inits.pad` - Sequence section wrapper

---

## Development Tools

The `develop/` directory contains utilities for development and testing:

### Build System
- `build/` - Build scripts for generating documentation
  - `index.php` - Main build orchestrator
  - `regression.php` - Regression test generator
  - `examples.php` - Example extractor
- `buildOld/` - Legacy build scripts (archived)

### Development Pages
- `index.pad` - Development tools home
- `all.pad/php` - Generate all pages at once
- `allright.pad/php` - Generate and verify all pages
- `manual.pad/php` - Manual page renderer
- `site.pad/php` - Site generation
- `go.pad/php` - Quick execution runner
- `nuts.pad/php` - Diagnostic utilities

### Benchmarking
- `benchmark/` - Performance benchmarking tools
  - `index.pad` - Benchmark interface

### Supporting Files
- `DATA/` - Build output data
  - `regression.txt` - Last processed test
  - `examples.txt` - Example generation log
- `_include/` - Build templates
- `_lib/` - Build utilities
- `_inits.php` - Development initialization

---

## Custom Tags

The `_tags/` directory implements 40+ custom application tags:

### Example System Tags
- `example.pad/php` - Display example with source/result
- `fragment.pad` - Include fragment with formatting
- `source.php` - Show syntax-highlighted source
- `manual.php` - Render manual page references

### Display Tags
- `demo.php` - Demo display wrapper
- `table.pad/php` - Enhanced table generation
- `construct.php` - Construct display
- `link.php` - Smart linking

### Utility Tags
- `getDir.php` - Directory listing
- `tag.php` - Tag metadata display
- `foo.php` - Test tag

### Error Tags
- `error_error.php` - Error demonstration
- `error_exception.php` - Exception demonstration
- `error_warning.php` - Warning demonstration
- `error_shutdown.php` - Shutdown handler demonstration

### Template Tags (z-series)
Multiple z-prefixed tags for specialized template processing:
- `z01.pad` through `z42.php` - Various template utilities
- Used internally by the manual and reference systems

---

## Library Functions

The `_lib/` directory provides 8 core library files:

### Utility Libraries
- `util.php` - General utilities (directory scanning, file operations)
- `file.php` - File handling functions
- `lib.php` - Core library functions
- `menu.php` - Menu generation

### Display Libraries
- `colors.php` - Syntax highlighting and colorization
- `reference.php` - Reference system utilities

### Specialized Libraries
- `sequence.php` - Sequence subsystem helpers
- `error_shutdown.php` - Error shutdown handling

### Key Functions
These libraries provide helper functions used throughout the application for:
- Building navigation menus
- Syntax highlighting code blocks
- Managing references and cross-links
- Handling file operations
- Processing sequence data
- Error handling and reporting

---

## Data Files

The `_data/` directory contains static data files for examples:

### XML Data
- `staff.xml` - Employee data (used in staff/ examples)
- `mondial.xml` / `mondial2.xml` - Geographic data
- `departments.xml` - Department hierarchy
- `level_demo.xml` - Level system demonstration
- `demo.xml` - General demo data
- `bakery.xml` - Bakery example data
- `PurchaseOrders.xml` - Purchase order examples

### JSON Data
- `files.json` - File listing data
- `xref.json` - Cross-reference metadata (in reference/_data/)

### Directories
- `array/` - Array data examples
- `record/` - Record data examples
- `departments/` - Department data files

---

## Database Schemas

The `_install/` directory contains SQL schemas for examples:

### Databases
- `classicmodels.sql` - Sample business database
  - Tables: employees, offices, customers, orders, orderdetails, products, productlines, payments
  - Used in: `db/`, `select/`, `manual/classicModels.pad`

- `demo.sql` - Demo application tables
  - Simple examples and demonstrations

- `database.sql` - Basic database setup
  - Core table definitions

### Usage
Import these schemas to enable database-related examples:
```bash
mysql < _install/classicmodels.sql
mysql < _install/demo.sql
```

---

## Entry Points and Special Files

### Main Entry Points
- `index.pad/php` - Application home page
  - Displays PAD concept and Hello World example
  - Links to manual, reference, regression, sequences

- `_inits.pad/php` - Global wrapper
  - Wraps all pages with navigation menu
  - Uses `?` placeholder for page content
  - Provides consistent layout

### Test Files
- `test.pad/php` - Quick test page for experiments

### Configuration
- `_config/config.php` - Application configuration
  - Database connection settings
  - PAD framework configuration overrides

---

## Running the Application

### Web Access

Start from the main URL: `http://server/pad/`

### Key URLs

| URL | Purpose |
|-----|---------|
| `?` or `?index` | Home page |
| `?manual` | Framework manual |
| `?reference` | Cross-reference system |
| `?regression` | Run regression tests |
| `?regression&sequence=1` | Run sequence-only tests |
| `?manual/sequence` | Sequence documentation |
| `?develop` | Development tools |
| `?{category}/{test}` | Run specific test |

### Examples
- `?hello/hello` - Hello World example
- `?tags/if` - If tag example
- `?sequence/basic/fibonacci` - Fibonacci sequence
- `?db/array` - Database array example

---

## Features Demonstrated

This application showcases the complete PAD framework feature set:

### Core Features
- Page pairing (`.php` data + `.pad` template)
- Global wrapper (`_inits.pad` with `?` placeholder)
- Inversion of control (templates drive PHP)
- Level system (nested tag processing)
- Occurrence system (data iteration)

### Template System
- 40+ built-in tags
- 40+ pipe functions
- 25+ iteration properties
- 50+ tag options
- Custom tags, functions, callbacks, options

### Data Integration
- Database operations (RECORD, ARRAY, FIELD, CHECK)
- PAD Select (declarative table access with auto-joins)
- XML/JSON/CSV/YAML data parsing
- File operations

### Advanced Features
- 80+ mathematical sequences with transformations
- Expression evaluation with pipes
- Variable scoping (level vs occurrence)
- Flow control (if, while, until, case, break, continue, cease)
- Content and data stores
- Callbacks for data processing
- Error handling and debugging

### Development Features
- Cross-reference documentation generation
- Automated regression testing
- Example extraction
- Syntax highlighting
- Build automation

---

## Application Statistics

- **32** test fragment categories
- **557** example template files
- **40+** custom tags
- **8** library files
- **20** manual pages
- **600+** sequence examples
- **80+** sequence types
- **260** reference DATA files
- **Comprehensive** regression testing
- **Complete** framework coverage

---

## Architecture

This application demonstrates PAD's procedural, file-based architecture:

- **No routing**: URL structure mirrors file structure (`?path/page` → `path/page.pad`)
- **No controllers**: Templates orchestrate data retrieval and display
- **Auto-loading**: `_lib/`, `_tags/`, `_functions/` automatically included
- **Nested wrappers**: `_inits.pad` at each directory level
- **Include-driven**: Control flow via file includes
- **Level-indexed**: All state tracked via `$pad` level counter

---

## Development Workflow

### Adding Test Fragments
1. Create `{category}/{test}.pad` template
2. Create `{category}/{test}.php` data file (if needed)
3. Run regression to generate reference: `?develop/build/regression`
4. View test: `?{category}/{test}`
5. Check regression: `?regression`

### Adding Manual Pages
1. Create `manual/{topic}.pad`
2. Add examples using `{example}` tag
3. Link from `manual/index.pad`
4. View: `?manual/{topic}`

### Regenerating Documentation
- **Regression references**: `?develop/build/regression`
- **Examples**: Auto-generated during regression
- **Reference DATA**: Auto-generated on access
- **All pages**: `?develop/all`

---

## For More Information

- **PAD Framework**: See [../../README.md](../../README.md)
- **Framework Internals**: See [../../CLAUDE.md](../../CLAUDE.md)
- **Online Manual**: `?manual`
- **Cross-Reference**: `?reference`
- **Sequence Manual**: `?manual/sequence`

---

## License

PAD is licensed under the GNU General Public License v3.0.
