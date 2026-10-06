# Common Resources

## Introduction

Shared resources and utilities the engine adds to every PAD application while $padCommon is TRUE, the default.

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

Nothing is linked: the engine adds `_common` to every application - its `_lib`, `_tags`, `_include`, `_data`, the `_inits.pad` wrapper and `_inits.php`/`_exits.php` - unless the application sets `$padCommon = FALSE` in its `_config/config.php`.
