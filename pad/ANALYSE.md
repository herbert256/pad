# PAD Framework Analysis

Code analysis for improvements, refactoring, and modernization opportunities.

---

## Current State Summary

### Strengths

- **Working Production Code**: 15+ years of real-world usage proves stability
- **PHP 8.0 Compatible**: Already uses `str_starts_with()`, `str_ends_with()`, `Throwable`
- **Consistent Naming**: Clear `pad` prefix convention throughout
- **Modular File Structure**: Logical directory organization
- **Comprehensive Error Handling**: Multi-level error strategy with environment awareness
- **Flexible Configuration**: Well-organized config system

### Architecture Characteristics

- **Procedural Design**: 140+ functions, minimal OOP
- **Global State Management**: Heavy use of `$GLOBALS` and `global` declarations
- **File-Based Dispatch**: `include` statements as control flow mechanism
- **Array-Indexed Levels**: Nesting tracked via `$padVariable[$pad]` pattern

---

## 1. PHP Version Modernization

### Current: PHP 8.0+

Already using modern features:
- `str_starts_with()`, `str_ends_with()`, `str_contains()`
- `Throwable` type for exception handling
- Named arguments (partial)

### Recommended: PHP 8.1+ Features

**Readonly Properties** (PHP 8.1)
```php
// Current: globals
global $padPage, $padDir;

// Modern: readonly class properties
readonly class PadContext {
    public function __construct(
        public string $page,
        public string $dir,
        public int $level,
    ) {}
}
```

**Enums** (PHP 8.1) for error levels, output types:
```php
// Current
$padErrorLevel = 'all';  // 'none', 'error', 'warning', 'notice', 'all'
$padOutputType = 'web';  // 'web', 'file', 'download', 'console'

// Modern
enum ErrorLevel: string {
    case None = 'none';
    case Error = 'error';
    case Warning = 'warning';
    case Notice = 'notice';
    case All = 'all';
}

enum OutputType: string {
    case Web = 'web';
    case File = 'file';
    case Download = 'download';
    case Console = 'console';
}
```

**Fibers** (PHP 8.1) for async operations:
```php
// Potential for async template rendering or parallel data fetching
```

**Constructor Property Promotion** (PHP 8.0):
```php
// For any future classes
class DatabaseConnection {
    public function __construct(
        private string $host,
        private string $user,
        private string $password,
        private string $database,
        private ?mysqli $connection = null,
    ) {}
}
```

---

## 2. Global State Reduction

### Current Pattern

```php
// Typical function
function padFieldValue ($name) {
    global $pad, $padCurrent, $padOccur, $padTag, $padData, $padFields;
    // ... uses globals throughout
}
```

### Problem

- 140+ `global $pad` declarations across 58 files
- Difficult to test in isolation
- Hidden dependencies between functions
- Risk of naming collisions

### Recommended Approach

**Option A: Context Object (Minimal Change)**

```php
// Create a context class
class PadContext {
    public int $level = -1;
    public array $current = [];
    public array $occur = [];
    public array $tag = [];
    public array $data = [];
    // ... other level-indexed arrays

    private static ?PadContext $instance = null;

    public static function get(): PadContext {
        return self::$instance ??= new PadContext();
    }
}

// Usage - gradually migrate
function padFieldValue($name, ?PadContext $ctx = null) {
    $ctx = $ctx ?? PadContext::get();
    $pad = $ctx->level;
    // ... use $ctx->current[$pad] instead of $padCurrent[$pad]
}
```

**Option B: Dependency Injection (Full Modernization)**

```php
class LevelProcessor {
    public function __construct(
        private PadContext $context,
        private TypeResolver $typeResolver,
        private ExpressionEvaluator $evaluator,
    ) {}

    public function process(string $content): string {
        // All dependencies explicit
    }
}
```

**Migration Path**:
1. Create `PadContext` class with static singleton
2. Add `$ctx` parameter to new functions
3. Gradually migrate existing functions
4. Keep backward compatibility via default parameter

---

## 3. Include-Based Dispatch Refactoring

### Current Pattern

```php
// level/level.php
if ( $padRestart )
    return include PAD . 'level/restart.php';

if ( file_exists ( PAD . "types/$padType.php" ) )
    include PAD . "types/$padType.php";
```

### Problems

- No autoloading
- File existence checks on every call
- Difficult to trace execution flow
- No IDE support for navigation

### Recommended Approach

**Strategy Pattern with Autoloading**

```php
// Type handlers as classes
namespace Pad\Types;

interface TypeHandler {
    public function handle(PadContext $ctx): mixed;
}

class AppType implements TypeHandler {
    public function handle(PadContext $ctx): mixed {
        // Logic from types/app.php
    }
}

class DataType implements TypeHandler {
    public function handle(PadContext $ctx): mixed {
        // Logic from types/data.php
    }
}

// Registry
class TypeRegistry {
    private array $handlers = [];

    public function register(string $type, TypeHandler $handler): void {
        $this->handlers[$type] = $handler;
    }

    public function get(string $type): ?TypeHandler {
        return $this->handlers[$type] ?? null;
    }
}
```

**Composer Autoloading**

```json
{
    "autoload": {
        "psr-4": {
            "Pad\\": "src/"
        },
        "files": [
            "src/functions.php"
        ]
    }
}
```

**Migration Path**:
1. Add `composer.json` with PSR-4 autoloading
2. Create interface for each handler type (TypeHandler, TagHandler, FunctionHandler)
3. Convert existing PHP files to classes implementing interfaces
4. Use registry pattern for dynamic dispatch
5. Keep `include` fallback for backward compatibility

---

## 4. Database Layer Modernization

### Current Pattern

```php
// lib/db.php
function db($sql, $vars = []) {
    global $padSqlConnect, $padSqlHost, $padSqlUser, $padSqlPassword, $padSqlDatabase;

    if (!isset($padSqlConnect))
        $padSqlConnect = padDbConnect($padSqlHost, $padSqlUser, $padSqlPassword, $padSqlDatabase);

    return padDbPart2($padSqlConnect, $sql, $vars);
}

// Custom placeholder replacement
$sql = str_replace('{0}', mysqli_real_escape_string($conn, $replace), $sql);
```

### Problems

- Global connection state
- Custom escaping (potential for mistakes)
- No prepared statements
- No query builder
- No connection pooling consideration

### Recommended: PDO with Prepared Statements

```php
class Database {
    private static ?PDO $connection = null;

    public static function connection(): PDO {
        if (self::$connection === null) {
            $config = Config::get();
            self::$connection = new PDO(
                "mysql:host={$config->dbHost};dbname={$config->dbName};charset=utf8mb4",
                $config->dbUser,
                $config->dbPassword,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        }
        return self::$connection;
    }

    public static function query(string $sql, array $params = []): array {
        $stmt = self::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function execute(string $sql, array $params = []): int {
        $stmt = self::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    public static function insertId(): string {
        return self::connection()->lastInsertId();
    }
}

// Usage
$users = Database::query(
    "SELECT * FROM users WHERE status = ? AND created > ?",
    ['active', '2024-01-01']
);
```

**Query Builder Option**

```php
class QueryBuilder {
    private string $table;
    private array $wheres = [];
    private array $params = [];

    public static function table(string $table): self {
        $builder = new self();
        $builder->table = $table;
        return $builder;
    }

    public function where(string $column, string $operator, mixed $value): self {
        $this->wheres[] = "$column $operator ?";
        $this->params[] = $value;
        return $this;
    }

    public function get(): array {
        $sql = "SELECT * FROM {$this->table}";
        if ($this->wheres) {
            $sql .= " WHERE " . implode(' AND ', $this->wheres);
        }
        return Database::query($sql, $this->params);
    }
}

// Usage
$users = QueryBuilder::table('users')
    ->where('status', '=', 'active')
    ->where('age', '>', 18)
    ->get();
```

**Migration Path**:
1. Add PDO wrapper class alongside existing mysqli
2. Create compatibility layer that translates `{0}` placeholders to `?`
3. Gradually migrate queries to prepared statements
4. Deprecate `db()` function in favor of new class

---

## 5. Configuration Modernization

### Current Pattern

```php
// config/config.php
$padErrorAction = 'pad';
$padErrorLevel = 'all';
$padSqlHost = '127.0.0.1';
// ... all as global variables
```

### Problems

- No type safety
- No validation
- No environment-based configuration
- Scattered across globals

### Recommended: Configuration Class

```php
class Config {
    private static ?Config $instance = null;
    private array $values = [];

    private function __construct() {
        $this->loadDefaults();
        $this->loadEnvironment();
        $this->loadFile();
    }

    public static function get(?string $key = null): mixed {
        $instance = self::$instance ??= new Config();

        if ($key === null) {
            return $instance;
        }

        return $instance->values[$key] ?? null;
    }

    private function loadDefaults(): void {
        $this->values = [
            'error.action' => 'pad',
            'error.level' => 'all',
            'error.log' => true,
            'db.host' => '127.0.0.1',
            'db.name' => 'app',
            'db.user' => 'app',
            'db.password' => '',
            'output.type' => 'web',
            'cache.enabled' => false,
        ];
    }

    private function loadEnvironment(): void {
        // Load from .env file or environment variables
        if ($host = getenv('PAD_DB_HOST')) {
            $this->values['db.host'] = $host;
        }
        // ... etc
    }

    private function loadFile(): void {
        $file = APP . '_config.php';
        if (file_exists($file)) {
            $config = include $file;
            $this->values = array_merge($this->values, $config);
        }
    }

    public function __get(string $name): mixed {
        $key = str_replace('_', '.', $name);
        return $this->values[$key] ?? null;
    }
}

// Usage
$host = Config::get('db.host');
// or
$config = Config::get();
$host = $config->db_host;
```

---

## 6. Error Handling Enhancement

### Current: Good Foundation

The framework already has sophisticated error handling. Enhancements:

### Recommended: PSR-3 Compatible Logging

```php
interface LoggerInterface {
    public function emergency(string $message, array $context = []): void;
    public function alert(string $message, array $context = []): void;
    public function critical(string $message, array $context = []): void;
    public function error(string $message, array $context = []): void;
    public function warning(string $message, array $context = []): void;
    public function notice(string $message, array $context = []): void;
    public function info(string $message, array $context = []): void;
    public function debug(string $message, array $context = []): void;
}

class PadLogger implements LoggerInterface {
    public function error(string $message, array $context = []): void {
        $formatted = $this->interpolate($message, $context);

        if (Config::get('error.log')) {
            error_log($formatted);
        }

        if (Config::get('error.report')) {
            $this->dumpToFile($formatted, $context);
        }
    }

    private function interpolate(string $message, array $context): string {
        $replace = [];
        foreach ($context as $key => $val) {
            $replace['{' . $key . '}'] = $val;
        }
        return strtr($message, $replace);
    }
}

// Usage
$logger = new PadLogger();
$logger->error('SQL error: {error} in query: {query}', [
    'error' => $mysqli_error,
    'query' => $sql,
]);
```

### Custom Exceptions

```php
namespace Pad\Exceptions;

class PadException extends \Exception {}
class TemplateException extends PadException {}
class DatabaseException extends PadException {}
class ConfigurationException extends PadException {}
class ValidationException extends PadException {}

// Usage
throw new DatabaseException("Connection failed: $error", previous: $e);
```

---

## 7. Testing Infrastructure

### Current: No Tests

No testing infrastructure exists.

### Recommended: PHPUnit Setup

**Directory Structure**
```
pad/
├── src/           # Future: refactored code
├── tests/
│   ├── Unit/
│   │   ├── EvalTest.php
│   │   ├── FunctionsTest.php
│   │   └── ValidationTest.php
│   ├── Integration/
│   │   ├── TemplateTest.php
│   │   └── DatabaseTest.php
│   └── bootstrap.php
├── phpunit.xml
└── composer.json
```

**phpunit.xml**
```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit bootstrap="tests/bootstrap.php">
    <testsuites>
        <testsuite name="Unit">
            <directory>tests/Unit</directory>
        </testsuite>
        <testsuite name="Integration">
            <directory>tests/Integration</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

**Example Tests**

```php
// tests/Unit/FunctionsTest.php
class FunctionsTest extends TestCase {
    public function testTrimFunction(): void {
        $value = '  hello  ';
        $result = include PAD . 'functions/trim.php';
        $this->assertEquals('hello', $result);
    }

    public function testUpperFunction(): void {
        $value = 'hello';
        $result = include PAD . 'functions/upper.php';
        $this->assertEquals('HELLO', $result);
    }

    /**
     * @dataProvider dateFormatProvider
     */
    public function testDateFunction(int $timestamp, string $format, string $expected): void {
        $value = $timestamp;
        $parm = [$format];
        $result = include PAD . 'functions/date.php';
        $this->assertEquals($expected, $result);
    }

    public static function dateFormatProvider(): array {
        return [
            [1702483200, 'Y-m-d', '2023-12-13'],
            [1702483200, 'H:i', '12:00'],
        ];
    }
}

// tests/Unit/ValidationTest.php
class ValidationTest extends TestCase {
    public function testValidName(): void {
        $this->assertTrue(padValid('userName'));
        $this->assertTrue(padValid('user_name'));
        $this->assertFalse(padValid(''));
        $this->assertFalse(padValid('123name'));
    }

    public function testValidFile(): void {
        define('APP', '/app/');
        define('DAT', '/data/');
        define('PAD', '/pad/');

        $this->assertTrue(padValidFile('/app/test.php'));
        $this->assertFalse(padValidFile('/etc/passwd'));
        $this->assertFalse(padValidFile('../../../etc/passwd'));
    }
}
```

---

## 8. Performance Optimizations

### File System

**Current**: Multiple `file_exists()` checks per request

```php
if (file_exists(PAD . "types/$type.php"))
    include PAD . "types/$type.php";
```

**Recommended**: Precomputed registry

```php
class FileRegistry {
    private static array $cache = [];

    public static function exists(string $path): bool {
        if (!isset(self::$cache[$path])) {
            self::$cache[$path] = file_exists($path);
        }
        return self::$cache[$path];
    }

    public static function warmup(): void {
        // Pre-scan directories on first request
        self::scanDirectory(PAD . 'types/');
        self::scanDirectory(PAD . 'tags/');
        self::scanDirectory(PAD . 'functions/');
    }
}
```

### OpCache Optimization

```php
// Ensure opcache is configured for production
// php.ini recommendations:
// opcache.enable=1
// opcache.memory_consumption=256
// opcache.interned_strings_buffer=16
// opcache.max_accelerated_files=10000
// opcache.validate_timestamps=0  (production only)
```

### Template Caching

```php
class TemplateCache {
    public static function get(string $template): ?string {
        $cacheFile = DAT . 'cache/' . md5($template) . '.php';

        if (file_exists($cacheFile)) {
            $cached = include $cacheFile;
            if ($cached['mtime'] >= filemtime(APP . $template)) {
                return $cached['content'];
            }
        }

        return null;
    }

    public static function set(string $template, string $compiled): void {
        $cacheFile = DAT . 'cache/' . md5($template) . '.php';
        $content = [
            'mtime' => time(),
            'content' => $compiled,
        ];
        file_put_contents($cacheFile, '<?php return ' . var_export($content, true) . ';');
    }
}
```

---

## 9. Security Enhancements

### SQL Injection Prevention

**Current**: Custom escaping with potential for bypass

```php
$sql = str_replace('{0}', mysqli_real_escape_string($conn, $replace), $sql);
```

**Recommended**: Prepared statements only (see Database section)

### Input Validation

```php
class Validator {
    public static function string(mixed $value, int $maxLength = 255): string {
        if (!is_string($value)) {
            throw new ValidationException('Expected string');
        }
        return substr($value, 0, $maxLength);
    }

    public static function int(mixed $value, int $min = PHP_INT_MIN, int $max = PHP_INT_MAX): int {
        $value = filter_var($value, FILTER_VALIDATE_INT);
        if ($value === false || $value < $min || $value > $max) {
            throw new ValidationException("Expected integer between $min and $max");
        }
        return $value;
    }

    public static function email(mixed $value): string {
        $value = filter_var($value, FILTER_VALIDATE_EMAIL);
        if ($value === false) {
            throw new ValidationException('Invalid email');
        }
        return $value;
    }
}
```

### CSRF Protection

```php
class CSRF {
    public static function token(): string {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function verify(string $token): bool {
        return hash_equals($_SESSION['csrf_token'] ?? '', $token);
    }

    public static function field(): string {
        return '<input type="hidden" name="_csrf" value="' . self::token() . '">';
    }
}
```

---

## 10. Code Quality Tools

### Recommended Tools

**composer.json**
```json
{
    "require-dev": {
        "phpunit/phpunit": "^10.0",
        "phpstan/phpstan": "^1.10",
        "squizlabs/php_codesniffer": "^3.7",
        "friendsofphp/php-cs-fixer": "^3.0"
    },
    "scripts": {
        "test": "phpunit",
        "analyse": "phpstan analyse src tests",
        "cs-check": "phpcs",
        "cs-fix": "php-cs-fixer fix"
    }
}
```

**phpstan.neon**
```neon
parameters:
    level: 6
    paths:
        - src
        - lib
    excludePaths:
        - tests
```

---

## Migration Strategy

### Phase 1: Foundation (Low Risk)
1. Add `composer.json` with autoloading
2. Add PHPUnit and write tests for existing functions
3. Add PHPStan at level 1, gradually increase
4. Create `Config` class (backward compatible)

### Phase 2: Core Improvements (Medium Risk)
1. Create `PadContext` singleton for gradual global reduction
2. Add PDO database wrapper alongside mysqli
3. Implement PSR-3 logging
4. Add custom exception classes

### Phase 3: Architecture (Higher Risk)
1. Convert type handlers to classes with interface
2. Convert tag handlers to classes with interface
3. Convert function handlers to classes with interface
4. Implement proper autoloading for handlers

### Phase 4: Full Modernization (Major Change)
1. Complete removal of global state
2. Full dependency injection
3. Event system refactoring
4. Template compilation/caching

---

## Summary

| Area | Current | Recommended | Priority |
|------|---------|-------------|----------|
| PHP Version | 8.0 | 8.1+ | Low |
| Global State | Heavy | Context object | High |
| Autoloading | None | PSR-4 | High |
| Database | mysqli + custom escape | PDO + prepared | High |
| Testing | None | PHPUnit | High |
| Configuration | Globals | Config class | Medium |
| Error Handling | Good | PSR-3 logging | Medium |
| Type System | Includes | Strategy pattern | Low |
| Code Quality | None | PHPStan + PHPCS | Medium |

The framework is functional and battle-tested. Modernization should be incremental, maintaining backward compatibility while gradually improving code quality, testability, and security.
