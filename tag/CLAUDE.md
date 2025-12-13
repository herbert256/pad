# tag/

Tag property accessors that return values about the current tag context.

## Available Properties
- `count.php` - Iteration count
- `current.php` - Current iteration data
- `data.php` - Tag data
- `key.php` - Current key
- `keys.php` - All keys
- `name.php` - Tag name
- `done.php` - Iterations completed

## Position Properties
- `first.php` - Is first iteration
- `last.php` - Is last iteration
- `middle.php` - Is middle iteration
- `notFirst.php` - Is not first
- `notLast.php` - Is not last
- `even.php` / `odd.php` - Even/odd iteration
- `border.php` - Is first or last
- `remaining.php` - Remaining iterations

## Field Properties
- `fields.php` - All fields
- `firstFieldName.php` - First field name
- `firstFieldValue.php` - First field value

## Parameter Properties
- `option.php` / `options.php` - Tag options
- `parameter.php` / `parameters.php` - Tag parameters
- `variable.php` / `variables.php` - Tag variables

## Usage
Accessed via `@property` syntax in templates, e.g., `{@count}`, `{@first}`, `{tagname@key}`.
