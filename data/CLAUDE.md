# data/

Data loading and format conversion handlers.

## Format Handlers
- `csv.php` - CSV file/string parsing
- `json.php` - JSON parsing
- `yaml.php` - YAML parsing
- `xml.php` - XML parsing
- `html.php` - HTML parsing
- `file.php` - Raw file loading
- `curl.php` - Remote URL fetching
- `list.php` - List/array data
- `range.php` - Numeric range generation

## Subdirectory
- `_lib/` - Shared data loading utilities

## Usage
These handlers convert various data formats into arrays that can be iterated over in templates using data tags.
