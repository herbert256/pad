# Build/Compilation Module

This module handles the build and compilation process for PAD pages, assembling page templates with library files, initialization/exit hooks, and managing the overall page structure.

## Overview

The build module orchestrates the construction of PAD pages by:
- Collecting library files from hierarchical directories
- Injecting initialization and exit hooks
- Assembling the complete page structure
- Managing the build directory hierarchy
- Integrating page-specific PHP and PAD template files

## Directory Structure

### Files
- **build.php** - Main build orchestrator that assembles all components
- **base.php** - Creates the base template structure with init/exit placeholders
- **page.php** - Processes individual page files with data integration
- **dirs.php** - Builds the directory hierarchy for the build process
- **split.php** - Splits build output into true/false branches
- **_lib.php** - Collects and includes library files from `_lib` directories

## Key Functions & Components

### build.php
Main build coordinator that:
1. Includes directory setup (`dirs.php`)
2. Collects library files (`_lib.php`)
3. Builds base template structure (`base.php`)
4. Processes page content (`page.php`)
5. Replaces `@pad@` placeholder with final page content
6. Triggers build event for info/debugging
7. Calls occurrence processing

**Global Variables Set:**
- `$padBase[$pad]` - Complete built page structure for current level

### dirs.php
Builds an array of directories in the application hierarchy.

**Process:**
1. Starts from the APP directory
2. Walks up the directory tree
3. Appends `$padDir` subdirectories
4. Creates `$padBuildDirs` array with all directories in hierarchy order

**Output:** `$padBuildDirs` - Array of directory paths from root to current

### _lib.php
Recursively includes all library files from `_lib` directories.

**Features:**
- Scans `_lib` subdirectories in all build directories
- Processes `.php` files using `call/once.php` (include once)
- Processes `.pad` files directly with `padFileGet()`
- Uses `RecursiveDirectoryIterator` for deep scanning

**Returns:** Concatenated string of all library content

### base.php
Creates the base template structure with initialization and exit hooks.

**Process:**
1. Starts with `@pad@` placeholder
2. For each directory in hierarchy:
   - Loads `_inits.pad` (initialization template)
   - Loads `_exits.pad` (exit/cleanup template)
   - Wraps `@pad@` with init and exit code
3. Builds nested structure from outermost to innermost

**Returns:** Template string with `@pad@` placeholder surrounded by all init/exit hooks

### page.php
Processes the actual page file and integrates with data.

**Process:**
1. Runs initialization files (`_inits.php`) for each directory
2. Includes page PHP file (`$padPage.php`) with `call/obNoOne.php`
3. Retrieves page template (`$padPage.pad`)
4. Runs exit files (`_exits.php`) for each directory
5. Handles data integration and `padBuild` wrapping

**Data Handling:**
- `FALSE` return → Use false branch
- `TRUE` return → Use true branch
- Array return → Wrap in `{padBuild for="$padPage"}...{/padBuild}`
- String return → Append to output
- Default data → Use content as-is

**Returns:** Complete page content string

### split.php
Splits build content into true/false conditional branches.

**Usage:** Separates content that should render conditionally based on page return values.

## Build Process Flow

```
1. build.php starts
   ↓
2. dirs.php creates directory hierarchy
   ↓
3. _lib.php collects all library files
   ↓
4. base.php creates template structure
   ├─ _inits.pad files
   ├─ @pad@ placeholder
   └─ _exits.pad files
   ↓
5. page.php processes page
   ├─ _inits.php execution
   ├─ page.php execution
   ├─ page.pad template
   └─ _exits.php execution
   ↓
6. Replace @pad@ with page content
   ↓
7. Store in $padBase[$pad]
   ↓
8. occurrence/occurrence.php processing
```

## File Naming Conventions

### Library Files
- **_lib/*.php** - PHP library files (included once)
- **_lib/*.pad** - PAD template libraries (concatenated)

### Initialization/Exit Files
- **_inits.php** - PHP initialization code (executed each build)
- **_inits.pad** - PAD template initialization (wrapped around content)
- **_exits.php** - PHP exit/cleanup code (executed after page)
- **_exits.pad** - PAD template exit/cleanup (wrapped around content)

### Page Files
- **pagename.php** - PHP page logic (returns data/boolean/string)
- **pagename.pad** - PAD template for the page

## Integration with PAD Framework

The build module is central to PAD's page generation:

- **Template Assembly**: Combines multiple template layers
- **Code Execution**: Runs PHP initialization and page logic
- **Data Flow**: Manages data between PHP and templates
- **Hierarchy Support**: Implements cascading directory structure
- **Event System**: Triggers build events for debugging/logging
- **Occurrence Processing**: Feeds built pages to occurrence handler

## Special Variables

### $padBuildDirs
Array of directory paths in the build hierarchy, from root to current page location.

### $padBuildLib
Concatenated string of all library file contents from `_lib` directories.

### $padBuildBase
Template structure with `@pad@` placeholder and all init/exit wrappers.

### $padBuildTrue / $padBuildFalse
Content for true/false conditional branches (from `split.php`).

### $padBuildCall
Return value from page PHP file, determines data handling mode.

## Usage Example

```php
// Application structure:
// /app/
//   ├─ _lib/
//   │  ├─ helpers.php
//   │  └─ functions.pad
//   ├─ _inits.php
//   ├─ _inits.pad
//   ├─ _exits.php
//   ├─ _exits.pad
//   └─ mypage/
//      ├─ _lib/
//      │  └─ page-helpers.php
//      ├─ _inits.php
//      ├─ mypage.php  (returns array data)
//      └─ mypage.pad  (template)

// build.php will:
// 1. Include _lib/helpers.php and page-helpers.php
// 2. Execute app/_inits.php and mypage/_inits.php
// 3. Execute mypage.php to get data
// 4. Wrap mypage.pad in {padBuild} tag
// 5. Surround with _inits.pad and _exits.pad from both levels
// 6. Execute _exits.php files
```

## Performance Considerations

- Library files are included once to avoid redefinition
- Uses output buffering for PHP file execution
- Directory iteration is done once per build
- Template concatenation is efficient string operations
