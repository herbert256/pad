# PAD Framework Documentation

PAD is a custom PHP framework and template engine designed for building dynamic web applications. It emphasizes modularity, custom tags, and helper functions.

## 1. Installation

### Prerequisites
- PHP
- MySQL
- Apache Web Server

### Setup
1.  **Clone the repository** to your server.
2.  **Run the installation scripts**. These scripts are located in the `install/` directory and must be run as root. They handle database creation, data directory setup, and Apache configuration.

    ```bash
    sudo install/install.sh
    ```

    *Note: You may need to adjust paths in `install/install.sh` and other scripts to match your specific system configuration.*

3.  **Configure your application**. In your application's entry point (e.g., `index.php`), you must define two constants before including `pad.php`:

    ```php
    define('APP', '/path/to/your/app');  // Directory for your app code and templates
    define('DAT', '/path/to/data');       // Writable directory for logs, cache, etc.
    require '/path/to/pad/pad.php';
    ```

## 2. Configuration

The main configuration file is located at `config/config.php`. Key settings include:

-   **Error Handling**:
    -   `$padErrorAction`: Controls how errors are handled ('pad', 'php', 'ignore', etc.).
    -   `$padErrorLevel`: Sets the PHP error reporting level.
    -   `$padErrorLog`: Whether to log errors to the Apache error log.

-   **Database**:
    -   `$padSqlHost`, `$padSqlDatabase`, `$padSqlUser`, `$padSqlPassword`: Credentials for the application database.
    -   `$padSqlPad...`: Credentials for the internal PAD database.

-   **Output**:
    -   `$padOutputType`: 'web', 'file', 'download', or 'console'.
    -   `$padGzip`: Enable GZIP compression.

-   **Formatting**:
    -   `$padFmtDate`, `$padFmtTime`: Default date and time formats.

## 3. Usage

PAD processes templates (typically `.pad` files) located in your `APP` directory. The engine parses these files, executing tags and functions to generate dynamic content.

### Basic Syntax
PAD templates mix HTML with special tags and function calls.

-   **Tags**: Enclosed in curly braces, e.g., `{if ...}`, `{end}`.
-   **Variables/Functions**: Also enclosed in curly braces, often with a pipe `|` for functions, e.g., `{$myVar | upper}`.

## 4. Tags

Tags are the building blocks of PAD templates. They control flow, include content, and manipulate data.

### Common Tags
-   **`{if condition}` ... `{elseif condition}` ... `{else}` ... `{/if}`**: Conditional logic.
-   **`{while condition}` ... `{/while}`**: Loops.
-   **`{include 'file.pad'}`**: Includes another template.
-   **`{content}`**: Used in layouts to insert the content of the child page.

### Creating Custom Tags
To create a new tag, add a PHP file to the `tags/` directory. The file name becomes the tag name (e.g., `tags/mytag.php` becomes `{mytag}`).

Inside the tag file:
-   `$padContent`: Contains the content inside the tag block (for paired tags).
-   `$padOpt`: Contains options/parameters passed to the tag.
-   Return `TRUE` or `FALSE` to control processing, or modify `$padContent` directly.

## 5. Functions

Functions are used to transform data. They are typically used within variable tags.

### Usage
```
{$userName | upper}
```
This takes the value of `$userName` and passes it to the `upper` function.

### Creating Custom Functions
Add a PHP file to the `functions/` directory (e.g., `functions/myfunc.php`).

Inside the function file:
-   `$value`: The input value to be transformed.
-   `$parms`: Array of additional parameters passed to the function.
-   **Return**: The transformed value.

Example (`functions/single/upper.php`):
```php
return strtoupper($value);
```

## 6. Directory Structure

-   **`_lib/`**: Core helper libraries.
-   **`config/`**: Configuration files.
-   **`functions/`**: Helper functions callable from templates.
-   **`tags/`**: Custom template tags.
-   **`start/`, `level/`**: Core template processing logic.
-   **`install/`**: Installation scripts.
-   **`pad.php`**: Main entry point.
