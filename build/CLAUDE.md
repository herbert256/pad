# build/

Constructs the initial template content by combining page files with wrapper templates.

## Process
1. `dirs.php` - Determines build directories to scan
2. `_lib.php` - Loads library/helper content
3. `base.php` - Builds the base template structure from `_inits.pad` and `_exits.pad` files
4. `page.php` - Loads the actual page content
5. `split.php` - Handles template splitting logic

## Template Placeholders
- `@pad@` - Placeholder for nested content insertion
- `@page@` - Placeholder for page content

## Flow
The build process wraps the page content in successive layers of init/exit templates from each build directory, creating a nested structure where `@pad@` markers are replaced with inner content.
