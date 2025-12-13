# PAD Framework Core Library Module

## Overview
The lib directory contains essential utility functions and helper libraries that provide core functionality used throughout the PAD framework. These libraries offer abstractions for common operations including database access, file handling, HTTP requests, field value retrieval, error handling, and output management.

## Purpose
This module provides foundational utilities for:
- Database operations and SQL query execution
- Field and variable value access across scopes
- HTTP/CURL requests and API interactions
- File reading and writing operations
- Output generation and HTTP header management
- Error handling and debugging
- Callback execution and event handling
- Type detection and validation
- Data dumping and inspection

## Directory Structure

### Core Library Files
- **db.php** - Database abstraction layer for MySQL/MariaDB
- **field/field.php** - Field value retrieval and validation system
- **api.php** - API utilities (redirects, restarts, fast links)
- **output.php** - HTTP output, headers, and caching
- **error.php** - Error handling and reporting
- **curl.php** - HTTP client with CURL support
- **file.php** - File system operations
- **callback.php** - Callback execution management
- **level.php** - Level/scope utility functions
- **type.php** - Type detection and validation
- **sequence.php** - Sequence operation utilities
- **content.php** - Content manipulation functions
- **dump.php** - Variable dumping and debugging
- **exit.php** - Framework exit handling
- **info.php** - Information and debugging utilities
- **options.php** - Options processing
- **parms.php** - Parameter handling utilities
- **page.php** - Page routing utilities
- **table.php** - Table data operations
- **valid.php** - Validation functions
- **data.php** - Data manipulation utilities
- **other.php** - Miscellaneous utility functions

### Subdirectories
- **field/** - Field access and manipulation
  - `field.php` - Main field functions
  - `at.php` - @ notation field access
  - `level.php` - Level-specific field access
  - `lib.php` - Field library functions
  - `parm.php` - Parameter field access
  - `prefix.php` - Prefix-based field access
  - `tag.php` - Tag field access
- **eval/** - Dynamic code evaluation utilities

## Key Features

### Database Operations (db.php)

Provides two main database connection functions:

**db($sql, $vars)** - Application database access
- Connects to application MySQL database
- Uses mysqli for secure connections
- Supports parameterized queries with placeholder replacement

**padDb($sql, $vars)** - PAD framework database access
- Separate connection for PAD system tables
- Same interface as db() function

**Query Types and Commands:**
- `SELECT` / `array` - Returns array of records
- `record` - Returns single record as associative array
- `field` - Returns single field value
- `check` - Returns boolean for record existence
- `INSERT` - Returns auto-increment ID or affected rows
- `UPDATE` / `DELETE` - Returns affected row count
- `REPLACE` / `TRUNCATE` / `SET` / `LOAD` - Returns row count

**Parameter Substitution:**
```php
db("SELECT * FROM users WHERE id={0}", [$userId]);
db("INSERT INTO users (name, email) VALUES ('{name}', '{email}')", $data);
db("UPDATE users SET name={name:50} WHERE id={id}", $data);  // Truncates to 50 chars
```

**Security Features:**
- Automatic mysqli_real_escape_string() on all parameters
- Prefix parameter with 'x' to bypass escaping: `{xRaw}`
- Length limiting with `{param:length}` syntax
- Traditional SQL mode for strict validation

### Field Value Access (field/field.php)

Comprehensive field access system with multiple accessor functions:

**Primary Functions:**
- `padFieldCheck($parm, $lvl)` - Check if field exists
- `padFieldValue($parm, $lvl)` - Get field value (returns '' if not found)
- `padArrayCheck($parm, $lvl)` - Check if array exists
- `padArrayValue($parm, $lvl)` - Get array value (returns [] if not found)
- `padOptCheck($parm)` - Check if option exists
- `padOptValue($parm)` - Get option value
- `padTagCheck($parm)` - Check if tag exists
- `padTagValue($parm, $lvl)` - Get tag value
- `padFieldNull($parm)` - Check if field is null

**Field Access Syntax:**
- Simple field: `fieldName`
- With prefix: `prefix:fieldName`
- With level: `level:fieldName`
- @ notation: `@record@field` or `field.property`
- Cross-level access: `parent:fieldName`

**Special Values:**
- `pad` - Returns $padGo (base URL)
- Field returns INF if not found (for internal processing)
- NULL indicates actual null value

**Helper Functions:**
- `padRawValue($parm)` - Get value with } escaped to &close;
- `padUrlValue($parm)` - Returns URL-encoded query parameter

### HTTP Client (curl.php)

Full-featured HTTP client with CURL support:

**padCurl($input)** - Execute HTTP request
- Supports GET and POST requests
- Cookie handling
- Custom headers
- Basic authentication
- Gzip compression
- Automatic redirects
- Content-type detection

**Input Parameters:**
- `['url']` - Required URL (supports file://, relative URLs, full URLs)
- `['get']` - Array of GET parameters
- `['post']` - Array of POST data
- `['user']` / `['password']` - Basic authentication
- `['cookies']` - Array of cookies
- `['headers']` - Array of custom headers
- `['options']` - Array of CURL options

**Output Structure:**
```php
[
  'url' => 'final URL',
  'result' => '200',  // HTTP status code
  'type' => 'json',   // Detected content type (xml/json/html/csv/yaml)
  'data' => 'response body',
  'headers' => [...], // Response headers
  'cookies' => [...], // Response cookies
  'info' => [...],    // CURL info array
  'stats' => [...]    // Performance stats (if enabled)
]
```

**Features:**
- Automatic content-type detection (XML, JSON, HTML, CSV, YAML)
- File path resolution for local files
- Automatic session cookie injection for same-host requests
- Fallback to file_get_contents() if CURL unavailable
- Error handling and reporting
- Integration with PAD event system

### File Operations (file.php)

Secure file system access with safety checks:

**padFileGet($file, $default)** - Read file contents
- Automatic path resolution (PAD, APP, DAT)
- Returns default value if file not found
- Security validation against directory traversal
- Special handling for `php://input`

**padFilePut($file, $data, $append)** - Write file contents
- Forces DAT directory for writes
- Auto-creates directories with proper permissions
- Supports append mode
- JSON encoding for arrays/objects
- File locking (LOCK_EX)

**Security Checks:**
- Must start with `/`
- No `..` directory traversal
- No `//` double slashes
- No control characters (0x00-0x1F, 0x7F)

**File Permissions:**
- Directories: Created with $padDirMode
- Files: Created with $padFileMode

### Output Management (output.php)

HTTP response and header management:

**padWebSend($stop)** - Send HTTP response
- Handles gzip compression
- Sets HTTP status code
- Sends headers
- Outputs content
- Supports FastCGI finish_request

**padWebHeaders($stop)** - Set HTTP headers
- PAD session/request ID header
- Performance statistics
- Cache-Control headers
- Content-Type and Content-Length
- ETag and Last-Modified

**padDownLoadHeaders($contentType, $fileName, $length)** - File download
- Sets Content-Disposition for downloads
- Binary transfer encoding
- File name and length headers

**Cache Control:**
- Client-side caching with max-age
- Proxy caching with s-maxage
- ETag validation
- Vary headers for compression
- Cache revalidation directives

### API Utilities (api.php)

Request routing and navigation:

**padRedirect($go, $vars)** - HTTP redirect
- Supports relative URLs
- Adds session/request IDs for same-host
- URL parameter encoding
- Special SELF:// protocol
- Sends 302 status code

**padRestart($go, $vars)** - Internal restart
- Restart framework with new parameters
- Maintains session context
- Returns NULL to halt current execution

**padFastLink($page, $vars)** - Generate fast link
- Creates temporary redirect in database
- Random token generation
- Stores serialized variables
- Returns full URL with token

### Callback Management (callback.php)

Execute user callbacks with variable isolation:

**padCallbackBeforeXxx($callbackType)** - Execute callback
- Isolates global variables
- Captures variable changes
- Restores clean state after execution
- Includes callback file

**padCallbackBeforeRow(&$padRowParm)** - Row callback
- Special handling for row data
- Passes row by reference
- Preserves existing $row variable
- Updates row data after callback

### Level Utilities (level.php)

Scope and tag manipulation:

**padLevel($value)** - Replace tag in output
- Replaces content between $padStart and $padEnd
- Updates $padOut buffer

**padPipeSplit($input)** - Split on pipe character
- Respects quoted strings (single and double)
- Handles escaped quotes
- Returns [$left, $right] array

**Comment and Tag Helpers:**
- `padCommentCheck()` - Detect {# #} comments
- `padCommentGo()` - Process comment
- `padLevelNo($no)` - Replace with no-op
- `padLevelNoSingle()` - No-op for unmatched single tags
- `padLevelNoPair()` - No-op for unmatched pairs
- `padLevelBetween()` - Extract content between delimiters
- `padLevelStart()` - Find opening {
- `padLevelEnd()` - Find closing }
- `padLevelNoOpen()` - Escape closing } without opening

### Type Detection (type.php)

Determine tag and variable types:

**padTypeTag($type, $goFunction)** - Detect tag type
Returns one of:
- `'app'` - Application tag
- `'pad'` - PAD framework tag
- `'pull'` - Sequence pull
- `'flag'` - Boolean flag
- `'content'` - Content store
- `'data'` - Data store
- `'include'` - File include
- `'field'` - Field value
- `'property'` - Object property
- `'array'` - Array value
- `'parm'` - Parameter
- `'level'` - Level reference
- `'constant'` - PHP constant
- `'local'` - Local data file
- `'script'` - Script execution
- `'php'` - PHP function
- `'page'` - Page route
- `'sequence'` - Sequence type
- `'action'` - Sequence action
- `'function'` - Function tag

**padTypeFunction($type, $goTag)** - Detect function type
- Similar to padTypeTag but prioritizes functions
- Falls back to tag detection if $goTag is true

**padTypeSeq($type, $item)** - Sequence type detection
- Handles action/make/start type combinations
- Sequence-specific type resolution

### Sequence Utilities (sequence.php)

Helper functions for sequence operations:

**Data Manipulation:**
- `pqActionArray(&$parms)` - Extract array from parameters
- `pqRandomParm(&$parm)` - Process random ranges (1..10)
- `pqShuffle(&$array)` - Shuffle preserving keys
- `pqRandom($array, $count, $order, $dups, $once)` - Random selection

**Sequence Checks:**
- `pqSeq($seq)` - Check if sequence exists
- `pqAction($action)` - Check if action exists
- `pqStore($check)` - Check if store type
- `pqPlay($check)` - Check if play type

**Array Operations:**
- `pqArray($sequence, $parm, $options)` - Execute sequence and return array
- `pqTruncate($array, $side, $count)` - Truncate from left/right
- `pqDone($option, &$array)` - Remove and check option

**Build Type Detection:**
- `pqBuild($check, $for)` - Determine build type
- Returns: loop, make, function, bool, order, build, fixed, generated, check

## Integration with PAD Framework

The library module provides essential services to all framework components:

1. **Database Layer** - Used by data tags, sequences, and type handlers
2. **Field Access** - Core to tag processing and variable resolution
3. **HTTP Client** - Powers external API calls and remote data fetching
4. **File System** - Template loading, data file access, caching
5. **Output System** - Final response generation and header management
6. **Type System** - Tag type detection and routing
7. **Event System** - Integrated debugging and monitoring
8. **Error Handling** - Framework-wide error reporting

## Usage Examples

### Database Query
```php
// Get user record
$user = db("record * FROM users WHERE id={id}", ['id' => $userId]);

// Get field value
$name = db("field name FROM users WHERE id={id}", ['id' => $userId]);

// Insert with auto-increment
$newId = db("INSERT INTO users (name, email) VALUES ('{name}', '{email}')", $data);
```

### Field Access
```php
// Simple field
$value = padFieldValue('userName');

// With level
$value = padFieldValue('parent:userName', 1);

// @ notation
$value = padFieldValue('@user@name');

// Check existence
if (padFieldCheck('userName')) { ... }
```

### HTTP Request
```php
$result = padCurl([
  'url' => 'https://api.example.com/data',
  'get' => ['key' => 'value'],
  'headers' => ['Authorization' => 'Bearer token']
]);

if ($result['result'] == '200') {
  $data = json_decode($result['data'], true);
}
```

### File Operations
```php
// Read file
$content = padFileGet('templates/header.html');

// Write file
padFilePut('cache/data.json', $jsonData);

// Append to log
padFilePut('logs/access.log', $logEntry, 1);
```

## Error Handling

Library functions provide error handling through:
- Return values (FALSE, empty string, empty array)
- padError() function calls for critical errors
- Exception catching in CURL and file operations
- Validation checks before operations

## Performance Considerations

- Database connections are persistent per request
- File operations include event tracking when $padInfo enabled
- CURL includes optional performance statistics
- Output compression with gzip when supported
- Efficient field lookup with INF sentinel value

## Security Features

- SQL injection prevention with parameterized queries
- File path validation and sanitization
- Directory traversal protection
- Control character filtering
- Length limiting on database inputs
- Proper file permissions on creation
- Cookie and session security for same-host requests
