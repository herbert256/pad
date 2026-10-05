# Structure Application

## Introduction

Demonstrates the PAD directory structure and how special `_xxx` directories work at different levels of the application hierarchy.

## Purpose

PAD applications can use special directories (prefixed with `_`) to organize functionality. These directories are automatically loaded and their contents become available in templates. This demo shows that these directories work not only at the application root, but also in subdirectories.

## Directory Structure

The root carries the complete set of `_xxx` directories. Each subdirectory overrides only one
or two names and inherits the rest, so every demo page shows which level answered each part:

```
structure/
├── _callbacks/          # Iteration callbacks          - myCallback
├── _config/             # Application configuration (root only)
├── _data/               # Static data files (XML, JSON) - myXML
├── _functions/          # Custom pipe functions         - myFunction
├── _include/            # Template snippets             - myInclude
├── _lib/                # Auto-included PHP functions
├── _options/            # Custom tag options            - myOption
├── _scripts/            # Shell scripts                 - myScript
├── _tags/               # Custom template tags          - myTag
├── _inits.php           # Runs before all pages
├── _inits.pad           # Wraps all pages (top)
├── _exits.php           # Runs after all pages
├── _exits.pad           # Wraps all pages (bottom)
├── index.php            # Home page (redirects to ?abc/klm/xyz/page, the deepest level)
├── page.pad             # Demo page template
├── page.php             # Demo page data
│
├── abc/                 # overrides _tags/myTag
│   └── klm/             # overrides _functions/myFunction
│       └── xyz/         # overrides _include/myInclude
│
└── def/                 # overrides _data/myXML and _scripts/myScript
    └── klm/             # overrides _callbacks/myCallback and _options/myOption
```

Every level also has its own `_lib/`, its wrappers (`_inits.php`, `_inits.pad`, `_exits.php`,
`_exits.pad`) and a `page.pad`/`page.php`: `_lib/` is cumulative and the wrappers nest, so
those show every level at once. Each line of a demo page names the directory that answered it.
The root is labelled `root` and every other level by its path, e.g. `abc/klm`.

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
- `?abc/klm/xyz/page` - Third subdirectory demo
- `?def/page` - Sibling branch demo (shows branch isolation)
- `?def/klm/page` - Sibling branch, second level

Each page shows its tag, function, include, data, script, callback and option, and which level answered each. `?abc/klm/xyz/page` takes `myTag` from `abc/`, `myFunction` from `abc/klm/`, `myInclude` from itself and the rest from the root. The `def/` branch shows that sibling branches inherit from the root but not from each other: `abc/`'s `myTag` never reaches `?def/page`.

## Key Concepts Demonstrated

1. **Nested wrappers** - Each subdirectory can add its own _inits.pad wrapper
2. **Local overrides** - Subdirectories can override parent _tags, _functions, etc.
3. **Cumulative _lib** - All _lib directories in the path are included
4. **Execution order** - PHP runs before PAD templates render
