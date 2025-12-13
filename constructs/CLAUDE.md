# constructs/

Template construct validators that determine if certain content patterns are present.

## Files
Each file returns TRUE if the construct is valid/present:
- `content.php` - Content construct check
- `end.php` - End construct check
- `start.php` - Start construct check
- `pad.php` - PAD construct check
- `page.php` - Page construct check
- `self.php` - Self-reference check
- `tidy.php` - Tidy construct check

## Usage
Used internally to validate template structure and determine processing paths.
