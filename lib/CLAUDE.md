# lib/

Core library functions used throughout PAD. All `.php` files are auto-loaded at startup.

## Key Files

### Data & Content
- `content.php` - Content merging and manipulation (`padContentMerge`, `padContentSet`)
- `data.php` - Data loading and manipulation
- `callback.php` - Callback utilities

### Evaluation
- `eval/` - Expression evaluation functions
  - `after.php` - Post-parse processing (`padEvalAfter`)
  - `types.php` - Type evaluation (`padEvalType`)

### Type System
- `type.php` - Type resolution (`padTypeTag`, `padTypeFunction`, `padTypeCommon`)

### Field Access
- `field/` - Field value access utilities

### Output
- `output.php` - Output generation and formatting
- `dump.php` - Debug dump utilities

### Database
- `db.php` - Database connection and queries
- `table.php` - Table/data iteration

### Utilities
- `other.php` - Miscellaneous utilities (`padInfo`, `padCloseSession`, etc.)
- `file.php` - File operations (`padFileGet`, `padFilePut`)
- `curl.php` - HTTP requests
- `valid.php` - Validation utilities
- `error.php` - Error utilities
- `exit.php` - Exit handling

### Tag Processing
- `options.php` - Tag option processing
- `parms.php` - Parameter utilities
- `page.php` - Page utilities
- `sequence.php` - Sequence utilities
- `level.php` - Level utilities
- `api.php` - API utilities
- `info.php` - Info utilities
