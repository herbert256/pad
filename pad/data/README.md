# PAD Data Format Handlers

This directory contains data format conversion handlers for the PAD framework. These handlers enable PAD to consume and process data from various sources and formats, converting them into PHP arrays for use within PAD applications.

## Overview

PAD's data handling system provides a unified interface for working with different data formats. Each handler in this directory is responsible for parsing a specific data format (CSV, JSON, XML, YAML, etc.) and converting it into a standardized PHP array structure. This abstraction allows PAD applications to work with diverse data sources without worrying about format-specific parsing logic.

## Files

### Data Format Handlers

- **csv.php** - CSV (Comma-Separated Values) parser
  - Parses CSV data into associative arrays
  - Handles quoted fields and embedded commas
  - Uses first row as header/column names
  - Supports embedded quotes (`""`) in fields
  - Returns array with column names as keys

- **json.php** - JSON data parser
  - Parses JSON objects and arrays
  - Handles special characters (`&open;`, `&close;` entity substitution)
  - Automatically detects and extracts JSON from surrounding text
  - Supports both JSON objects (`{}`) and arrays (`[]`)
  - Returns PHP associative array or indexed array
  - Provides detailed error messages on parse failures

- **xml.php** - XML data parser
  - Converts XML documents to PHP arrays
  - Delegates to XML library (`_lib/xml.php`)
  - Handles complex nested structures
  - Preserves XML attributes and element relationships

- **yaml.php** - YAML data parser
  - Parses YAML documents using PHP's yaml extension
  - Converts YAML structures to PHP arrays
  - Returns error on parse failure
  - Requires PHP yaml extension

- **html.php** - HTML to XML converter
  - Converts HTML to well-formed XML using PHP Tidy
  - Cleans and repairs malformed HTML
  - Parses resulting XML into PHP array structure
  - Useful for scraping or processing HTML content

- **file.php** - File content loader
  - Loads data from files using PAD's file handling functions
  - Determines file type and applies appropriate parser
  - Validates file paths using `padDataFileName()`
  - Retrieves file content using `padDataFileData()`

- **curl.php** - HTTP/URL data fetcher
  - Fetches data from URLs using cURL
  - Automatically detects content type from response
  - Applies appropriate parser based on content type
  - Returns error if HTTP request fails (non-2xx status)
  - Supports various HTTP methods and options

- **list.php** - List/array parser
  - Parses comma-separated list syntax
  - Evaluates each item using `padEval()`
  - Converts string lists to PHP arrays
  - Useful for inline data specification

- **range.php** - Range generator
  - Generates numeric or alphabetic ranges
  - Delegates to `padGetRange()` function
  - Creates arrays from range specifications (e.g., "1-10", "A-Z")

## Subdirectories

### _lib/

Contains shared library functions used by multiple data handlers.

- **xml.php** - XML to Array conversion library
  - Core XML parsing implementation
  - Functions:
    - `padXmlToArray()` - Main conversion function
    - `padXmlToArrayIterator()` - Recursive XML tree traversal
    - `padXmlToArrayCheck()` - Array structure cleanup and optimization
  - Handles XML attributes, children, and nested structures
  - Uses PHP's SimpleXML and Tidy libraries
  - Provides robust error handling for malformed XML
  - Flattens single-element arrays for cleaner output
  - Merges child elements with parent structure

## Key Features

### Unified Data Interface

All handlers return PHP arrays in a consistent format:
- Successful parsing returns an array (associative or indexed)
- Failures call `padError()` with descriptive error messages
- Enables predictable error handling across all formats

### Automatic Format Detection

Some handlers (like `file.php` and `curl.php`) can:
- Detect data format from context (file extension, MIME type)
- Apply the appropriate parser automatically
- Chain handlers for complex processing

### Error Handling

Consistent error handling across all parsers:
- Validation of input data
- Descriptive error messages
- Integration with PAD's error system via `padError()`
- Graceful handling of malformed data

### Special Character Handling

Handlers include support for:
- UTF-8 encoding (CSV handler)
- HTML entity substitution (JSON handler)
- Non-breaking spaces (XML handler)
- Quote escaping and special characters

### Data Cleanup

Many handlers perform cleanup operations:
- Whitespace trimming
- Empty element removal
- Structure optimization
- Character encoding normalization

## Integration with PAD Framework

The data handlers integrate seamlessly with PAD's data processing pipeline:

1. **Data Sources**: PAD applications can reference data from multiple sources:
   - Files (local file system)
   - URLs (HTTP/HTTPS endpoints)
   - Inline data (embedded in templates)
   - Database queries (processed elsewhere)

2. **Format Detection**: PAD detects or specifies the data format:
   - File extension
   - MIME type
   - Explicit format specification in PAD constructs

3. **Handler Invocation**: Appropriate handler is loaded:
   - Handler file is included
   - `$data` variable contains raw data
   - Handler returns parsed array or error

4. **Data Usage**: Parsed data becomes available to PAD templates:
   - Iterate through records
   - Display in tables or lists
   - Transform and manipulate
   - Combine with other data sources

5. **Error Recovery**: Failed parsing triggers PAD's error handling:
   - Error logged based on configuration
   - Alternative data source or fallback content
   - User-friendly error display

## Usage Patterns

### Direct Handler Usage

Handlers are typically invoked internally by PAD, but can be used directly:
```php
$data = file_get_contents('data.json');
$result = include PAD . 'data/json.php';
// $result now contains parsed array
```

### Common Data Flow

1. PAD construct references data source
2. Data is fetched (from file, URL, or inline)
3. Format is determined
4. Appropriate handler is invoked with `$data` variable
5. Handler returns array
6. Array is made available to template
7. Template iterates/displays data

### Chained Processing

Handlers can be chained:
- `curl.php` fetches data and determines type
- Calls appropriate parser (JSON, XML, etc.)
- Returns final array structure

## Supported Formats

- CSV - Comma-separated values with headers
- JSON - JavaScript Object Notation
- XML - Extensible Markup Language
- YAML - YAML Ain't Markup Language
- HTML - Hypertext Markup Language (converted to structured data)
- List - Comma-separated inline lists
- Range - Numeric or alphabetic ranges
- File - Auto-detected from file extension
- URL - Auto-detected from HTTP content type

## Dependencies

Some handlers require PHP extensions:
- **yaml.php** requires PHP YAML extension
- **xml.php** and **html.php** require PHP Tidy extension
- **xml.php** requires PHP SimpleXML extension
- **curl.php** requires PHP cURL extension

## Architecture Notes

The data handling architecture demonstrates PAD's philosophy:
- Simple, focused handlers for each format
- Shared libraries for common operations
- Consistent error handling and return values
- Extensibility through new handler files
- Integration with PAD's variable system

This modular approach allows developers to easily add support for new data formats by creating new handler files following the established patterns.
