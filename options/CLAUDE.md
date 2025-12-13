# options/

Tag option handlers that modify tag behavior.

## Start Options (processed before tag execution)
- `before.php` - Content to prepend
- `ignore.php` - Ignore tag processing
- `print.php` - Debug print option
- `demand.php` - Require tag to exist

## End Options (processed after tag execution)
- `toBool.php` - Convert result to boolean
- `toContent.php` - Convert to content store
- `toData.php` - Convert to data store
- `tidy.php` - Apply tidy to result
- `dump.php` - Dump result for debugging

## Content Options
- `content.php` - Content manipulation
- `else.php` - Else clause handling
- `data.php` - Data binding
- `glue.php` - Join array elements

## Control Options
- `bool.php` - Boolean evaluation
- `callback.php` - Callback execution
- `close.php` / `open.php` - Wrapper elements
- `quote.php` - Quote handling
- `optional.php` - Make tag optional
- `noError.php` - Suppress errors

## Subdirectory
- `go/` - Option execution helpers

## Usage
Options are specified in tag syntax: `{tag option1 option2=value}...{/tag}`
