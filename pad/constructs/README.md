# PAD Constructs Directory

This directory contains handler files for PAD's core XML-like constructs. These constructs form the fundamental building blocks of PAD templates and define the structure and behavior of PAD pages.

## Overview

PAD uses XML-like constructs (tags) to build dynamic web pages. Each construct has a corresponding PHP file in this directory that defines its behavior. When PAD encounters a construct in a template, it loads and executes the associated handler file. These handlers return boolean values indicating whether to continue processing child elements and content.

The constructs in this directory represent PAD's core structural elements that control page layout, content processing, and output formatting.

## Files

### Core Construct Handlers

- **pad.php** - Handler for the `<pad>` construct
  - Root-level construct for PAD pages
  - Returns `TRUE` to allow processing of child elements
  - Represents the main PAD document container

- **page.php** - Handler for the `<page>` construct
  - Defines individual pages within a PAD application
  - Returns `TRUE` to process page content
  - May contain content, forms, data displays, and other elements

- **content.php** - Handler for the `<content>` construct
  - Defines content sections within pages
  - Returns `TRUE` to process nested content
  - Used for organizing and structuring page content

- **start.php** - Handler for the `<start>` construct
  - Initialization or starting point construct
  - Returns `TRUE` to continue processing
  - May be used for setup operations at the beginning of a section

- **end.php** - Handler for the `<end>` construct
  - Finalization or ending point construct
  - Returns `TRUE` to complete processing
  - May be used for cleanup operations at the end of a section

- **self.php** - Handler for the `<self>` construct
  - Self-referential construct
  - Returns `TRUE` to process itself
  - Used for recursive or self-contained operations

- **tidy.php** - Handler for the `<tidy>` construct
  - HTML output formatting construct
  - Returns `TRUE` to apply tidying
  - Controls when and how HTML tidying is applied to output

## Key Features

### Simple Boolean Return Pattern

All construct handlers in this directory follow a simple pattern:
```php
<?php
  return TRUE;
?>
```

This pattern indicates that:
- The construct is valid and recognized
- Processing should continue with child elements
- No special processing logic is needed at this level (logic may be handled elsewhere in the framework)

### Construct Processing Flow

1. PAD parser encounters a construct tag in the template
2. Framework looks for a handler file matching the construct name
3. Handler is loaded and executed
4. Return value determines whether child elements are processed
5. Processing continues based on the construct hierarchy

### Extensibility

The construct system is extensible:
- New constructs can be added by creating new handler files
- Application-specific constructs can override core constructs
- Handlers can contain complex logic or remain simple validators

## Integration with PAD Framework

The constructs directory is fundamental to PAD's template processing:

1. **Template Parsing**: When PAD parses a template file, it identifies construct tags
2. **Handler Loading**: For each construct, PAD loads the corresponding handler from:
   - `/app/constructs/` (application-specific handlers, checked first)
   - `/pad/constructs/` (core framework handlers, this directory)
3. **Execution**: Handler is executed to determine processing behavior
4. **Processing Control**: Return value controls whether child elements are processed
5. **Nesting**: Constructs can be nested, creating a hierarchical structure
6. **Output Generation**: Processed constructs generate the final output

### Typical Construct Hierarchy

```xml
<pad>
  <page name="home">
    <content>
      <start />
      <!-- Page content here -->
      <end />
    </content>
  </page>
</pad>
```

## Usage in PAD Templates

Constructs are used in PAD template files (typically `.pad` files) to structure pages:

- `<pad>` wraps the entire PAD application
- `<page>` defines individual pages or views
- `<content>` organizes sections within pages
- `<start>` and `<end>` mark boundaries of processing sections
- `<self>` enables self-referential operations
- `<tidy>` controls HTML formatting of output

## Architecture Notes

The simplicity of these handlers (all returning `TRUE`) suggests that:
- The core processing logic resides in PAD's main processing engine
- These handlers serve primarily as validators/markers
- Complex behavior is implemented through attributes and nested content
- The framework uses the construct hierarchy to control processing flow
- Actual functionality may be triggered by the construct's attributes, context, or position in the document tree

This design allows PAD to maintain a clean separation between structure (constructs) and behavior (framework logic), making the system more maintainable and extensible.
