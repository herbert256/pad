# Common Resources

Shared resources and utilities available to all PAD applications via symbolic links or includes.

## Structure

```
_common/
├── _config/       # Shared configuration
├── _data/         # Shared data files
├── _exits.php     # Common exit processing
├── _include/      # Shared template snippets
├── _inits.pad     # Common HTML wrapper
├── _inits.php     # Common initialization (title extraction)
├── _lib/          # Shared PHP functions
└── _tags/         # Shared custom tags
```

## Features

- **Global wrapper**: `_inits.pad` provides a basic HTML structure with `@page@` placeholder
- **Title extraction**: `_inits.php` automatically derives page title from URL path
- **Shared libraries**: Common functions available across applications
- **Reusable tags**: Custom tags that can be used by multiple apps

## Usage

Applications can link to `_common` directories to inherit shared functionality without duplicating code.
