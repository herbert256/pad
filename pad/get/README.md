# PAD Get Directory

## Overview

The `get/` directory implements the variable getter system for the PAD framework. It provides the core logic for retrieving content, includes, pages, and tags within PAD templates.

## Purpose in PAD Framework

This module handles all variable resolution and content retrieval operations in PAD. When a variable or tag is referenced in a PAD template, this module determines how to fetch and return the appropriate content, whether from a content store, an include file, a page, or a custom tag.

## Files and Subdirectories

### Main Files
- **content.php** - Main content getter that orchestrates retrieval from multiple sources
  - Checks content store cache (`$padContentStore`)
  - Falls back to include checking
  - Falls back to page checking
  - Handles custom tags via `padTagAsFunction()`

- **include.php** - Handles include file retrieval
  - Resolves include file path via `padAppIncludeCheck()`
  - Delegates to `get/go/call.php` for execution

- **page.php** - Handles page retrieval
  - Resolves page file path via `padAppPageCheck()`
  - Delegates to `get/go/call.php` for execution

### Subdirectories
- **go/** - Contains the call execution logic
  - **call.php** - Executes includes and pages by loading `.php` and `.pad` files

## Key Functions

### Content Resolution Flow
1. **Cache Lookup**: First checks `$padContentStore` for cached content
2. **Include Resolution**: Attempts to resolve as an application include file
3. **Page Resolution**: Attempts to resolve as an application page
4. **Tag Resolution**: Falls back to custom tag processing if type is tag
5. **Default**: Returns empty string if no match found

### File Execution (call.php)
- Looks for both `.php` and `.pad` files for each include/page
- Executes PHP files using output buffering (`call/obNoOne.php`)
- Reads `.pad` files directly via `padFileGet()`
- Concatenates results if both files exist

## How This Module Fits Into PAD Architecture

### Variable Resolution Pipeline
```
Template Variable Reference
    |
    v
content.php (main dispatcher)
    |
    +-- Content Store (cached)
    |
    +-- Include Check --> include.php --> go/call.php
    |
    +-- Page Check --> page.php --> go/call.php
    |
    +-- Tag Check --> padTagAsFunction()
    |
    v
Resolved Content
```

### Integration Points
- **Used by**: PAD expression evaluator when resolving variables
- **Uses**: Application-level include/page checkers, tag system, file system
- **Caches**: Results stored in `$padContentStore` for performance
- **Delegates**: File execution to `call/` directory utilities

### Design Patterns
- **Strategy Pattern**: Different retrieval strategies (store, include, page, tag)
- **Lazy Loading**: Content only retrieved when requested
- **Caching**: Content store prevents repeated file system access
- **Separation of Concerns**: Path resolution separate from execution

This module is essential to PAD's template system, providing the flexible content retrieval mechanism that allows templates to reference includes, pages, and dynamic content seamlessly.
