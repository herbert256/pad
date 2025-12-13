# at/

Handles the `@` property access syntax for accessing tag properties, groups, and nested data.

## Syntax
- `{@property}` - Access a property at current level
- `{tag@property}` - Access property of specific tag
- `{tag@level:group}` - Access group data at specific level
- `{name.subkey@property}` - Nested property access

## Structure
- `_lib/` - Core functions (`padAt`, `padAtValue`, `padAtSearch`, etc.)
- `properties/` - Individual property handlers (count, first, last, key, data, etc.)
- `groups/` - Group data handlers (current, variables, parameters, options, etc.)
- `types/` - Type-specific property access (data, globals, sequences, tags)
- `any/` - Wildcard property handlers

## Key Functions (in `_lib/at.php`)
- `padAt()` - Main entry point for @ syntax evaluation
- `padAtValue()` - Resolves property value from field specification
- `padAtProperties()` - Handles property file lookups
- `padAtGroups()` - Handles group data access
- `padAtSearch()` - Searches arrays for nested keys

## Properties Available
count, current, data, done, even, odd, first, last, middle, key, keys, name, border, notFirst, notLast, remaining, fields, options, parameters, variables
