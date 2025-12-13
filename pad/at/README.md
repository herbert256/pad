# @ Symbol Property Accessor Module

This module provides the core functionality for accessing properties, variables, and data through the `@` symbol syntax in PAD templates. It enables dynamic property resolution across multiple scopes and data sources.

## Overview

The @ accessor module implements PAD's powerful property access syntax, allowing templates to reference data using patterns like `name@level:property` or `field@level:group`. This module searches through multiple data stores, global variables, tag properties, and other sources to resolve property references.

## Module Architecture

The @ module searches for property values in a cascading order:
1. Tag properties (first, last, count, etc.)
2. Level/group combinations
3. Property values
4. Group data (variables, parameters, options, etc.)
5. Type-specific data (sequences, globals, tags)
6. Data stores (pqStore, padDataStore)
7. Global variables and PHP superglobals

## Directory Structure

### Core Files (`_lib/`)
- **at.php** - Main @ accessor entry point with `padAt()` function and value resolution logic
- **lib.php** - Data retrieval and store management functions
- **search.php** - Array search and navigation with conditional logic and wildcard support
- **check.php** - Validation functions for @ syntax patterns

### Any (`any/`)
- **tag.php** - Tag-level property access
- **tags.php** - Multiple tag handling

### Groups (`groups/`)
- **any.php** - Generic group accessor
- **current.php** - Current iteration data access
- **function.php** - Function-related data
- **level.php** - Level-specific data
- **options.php** - Tag options access
- **parameters.php** - Tag parameters access
- **saved.php** - Saved/cached values
- **tables.php** - Table data access
- **variables.php** - Variable scope management

### Properties (`properties/`)
- **border.php** - Border position detection
- **count.php** - Iteration count
- **current.php** - Current item value
- **data.php** - Data access
- **done.php** - Completion status
- **even.php** - Even iteration check
- **fields.php** - Field list access
- **first.php** - First iteration check
- **key.php** - Current key access
- **keys.php** - All keys access
- **last.php** - Last iteration check
- **middle.php** - Middle iteration check
- **name.php** - Name property
- **notFirst.php** - Not first iteration check
- **notLast.php** - Not last iteration check
- **odd.php** - Odd iteration check
- **options.php** - Options list
- **parameters.php** - Parameters list
- **remaining.php** - Remaining iterations count
- **variables.php** - Variables list

### Types (`types/`)
- **all.php** - All types accessor
- **data.php** - Data type handling
- **globals.php** - Global variable access
- **sequences.php** - Sequence data access
- **tags.php** - Tag type handling
- **_lib/check.php** - Type validation
- **_lib/new.php** - New type creation
- **_lib/other.php** - Additional type handlers

## Key Functions

### padAt($field, $cor=0)
Main entry point for @ property access. Validates and resolves @ syntax patterns.

**Parameters:**
- `$field` - The @ syntax field to resolve (e.g., "name@1:count")
- `$cor` - Correction/offset value for level indexing

**Returns:** The resolved value or `INF` if not found

### padAtValue($field, $cor=0)
Core value resolution function that orchestrates the search through different data sources.

### padAtSearch($current, $names, $noDeep=0)
Recursively searches through nested arrays to find property values.

**Features:**
- Dot notation navigation (e.g., "user.profile.name")
- Wildcard support with `*`
- Conditional filtering (e.g., "items.age>18")
- Index-based access (e.g., "items.1<" for first item)
- Deep recursive searching through nested structures

### padAtIdx($level, $cor)
Resolves level identifiers to actual pad stack indices.

**Supports:**
- Named levels
- Numeric indices (0-based)
- Negative offsets (e.g., "-1" for previous level)
- Tag name matching

## Usage Examples

```php
// Access iteration count at level 1
$count = padAt('name@1:count');

// Access current item property
$value = padAt('item@2:current.title');

// Access variable at current level
$var = padAt('myvar@*:variables');

// Conditional array access
$filtered = padAt('users.age>18@1:data');

// Access first element
$first = padAt('items.1<@1:data');

// Access nested property
$nested = padAt('config.database.host@1:options');
```

## Integration with PAD Framework

The @ accessor module is fundamental to PAD's template system:

- **Template Rendering**: Resolves dynamic property references in templates
- **Data Binding**: Connects template syntax to PHP data structures
- **Iteration Control**: Provides iteration metadata (first, last, count, etc.)
- **Scope Management**: Navigates through nested template levels
- **Type System**: Integrates with PAD's type and data management

## Search Capabilities

The module supports advanced array searching:

- **Dot notation**: Navigate nested arrays (`user.address.city`)
- **Wildcards**: Random selection with `*`
- **Comparisons**: Filter arrays using `<`, `>`, `<=`, `>=`, `=`, `<>`
- **Indexing**: Access by position (`1<` = first, `1>` = last)
- **Deep search**: Automatically searches nested structures
- **Type conversion**: Handles objects and resources as arrays

## Performance Notes

- Uses `INF` constant to indicate "not found" (more efficient than exceptions)
- Implements early returns to minimize unnecessary searching
- Caches resolved values in global stores
- Supports lazy loading of data through `padDataNew()`
