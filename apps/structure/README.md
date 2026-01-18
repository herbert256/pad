# Structure - PAD Application Directory Demo

This application demonstrates the PAD directory structure and how special `_xxx` directories work at different levels of the application hierarchy.

## Purpose

PAD applications can use special directories (prefixed with `_`) to organize functionality. These directories are automatically loaded and their contents become available in templates. This demo shows that these directories work not only at the application root, but also in subdirectories.

## Directory Structure

```
structure/
├── _callbacks/          # Iteration callbacks
├── _config/             # Application configuration
├── _data/               # Static data files (XML, JSON)
├── _functions/          # Custom pipe functions
├── _include/            # Template snippets
├── _lib/                # Auto-included PHP functions
├── _options/            # Custom tag options
├── _scripts/            # Shell scripts
├── _tags/               # Custom template tags
├── _inits.php           # Runs before all pages
├── _inits.pad           # Wraps all pages (top)
├── _exits.php           # Runs after all pages
├── _exits.pad           # Wraps all pages (bottom)
├── index.php            # Home page data
├── page.pad             # Demo page template
├── page.php             # Demo page data
│
├── abc/                 # First-level subdirectory
│   ├── _callbacks/      # Subdirectory callbacks
│   ├── _functions/      # Subdirectory functions
│   ├── _include/        # Subdirectory includes
│   ├── _lib/            # Subdirectory lib
│   ├── _options/        # Subdirectory options
│   ├── _tags/           # Subdirectory tags
│   ├── _inits.pad       # Subdirectory wrapper (top)
│   ├── _exits.pad       # Subdirectory wrapper (bottom)
│   ├── page.pad
│   │
│   └── klm/             # Second-level subdirectory
│       ├── _callbacks/
│       ├── _functions/
│       ├── _include/
│       ├── _lib/
│       ├── _options/
│       ├── _tags/
│       ├── _inits.pad
│       ├── _exits.pad
│       └── page.pad
```

## Special Directories

| Directory | Purpose | Auto-loaded |
|-----------|---------|-------------|
| `_lib/` | PHP helper functions | Yes, all `.php` files |
| `_tags/` | Custom template tags | On demand |
| `_functions/` | Custom pipe functions | On demand |
| `_include/` | Template snippets | On demand |
| `_callbacks/` | Iteration callbacks | On demand |
| `_options/` | Tag option handlers | On demand |
| `_data/` | Static data files | On demand |
| `_config/` | Configuration files | On startup |
| `_scripts/` | Shell scripts | On demand |

## Wrapper Files

### _inits.pad and _exits.pad

These files wrap page content at each directory level:

```
/_inits.pad        ← Root wrapper (top)
  /abc/_inits.pad  ← Subdirectory wrapper (top)
    [page content]
  /abc/_exits.pad  ← Subdirectory wrapper (bottom)
/_exits.pad        ← Root wrapper (bottom)
```

### _inits.php and _exits.php

PHP files execute in a specific order:

1. `/_inits.php`
2. `/abc/_inits.php`
3. `/abc/klm/_inits.php`
4. `/abc/klm/page.php`
5. `/abc/klm/_exits.php`
6. `/abc/_exits.php`
7. `/_exits.php`

All PHP code runs before template rendering starts.

## Inheritance

When accessing a page in a subdirectory:

- **_lib/** files from all parent directories are included
- **_inits.pad** from each level wraps the content
- **_tags/**, **_functions/**, etc. are searched from current directory up to root

Example: Accessing `?abc/klm/page` will:
1. Include `/_lib/*.php`, `/abc/_lib/*.php`, `/abc/klm/_lib/*.php`
2. Wrap with `/_inits.pad` → `/abc/_inits.pad` → `/abc/klm/_inits.pad`
3. Look for tags in `/abc/klm/_tags/` first, then `/abc/_tags/`, then `/_tags/`

## Demo Pages

- `?page` - Root level demo
- `?abc/page` - First subdirectory demo
- `?abc/klm/page` - Second subdirectory demo

Each page demonstrates tags, functions, includes, callbacks, and options from its directory level.

## Key Concepts Demonstrated

1. **Nested wrappers** - Each subdirectory can add its own _inits.pad wrapper
2. **Local overrides** - Subdirectories can override parent _tags, _functions, etc.
3. **Cumulative _lib** - All _lib directories in the path are included
4. **Execution order** - PHP runs before PAD templates render
