# PAD

PAD is a collection of PHP scripts for building dynamic web applications. It provides functionality for template handling, error management, and database access. The framework relies on a set of directories that contain reusable components and configuration files.

## Project layout

The repository contains several directories that make up the PAD runtime:

- **_lib** – core helper libraries (database access, error handling, API utilities and more)
- **build** – scripts for building pages and aggregating library files
- **call**, **callback** – utility functions that load and execute PHP snippets
- **config** – default configuration files (error handling, output type, database credentials)
- **database** – SQL scripts used during installation
- **error** – boot‐time and runtime error handlers
- **events** – optional hooks that log or trace application state
- **functions** – helper functions callable from templates
- **inits** – initialization constants and setup code
- **install** – shell scripts for setting up Apache, MySQL and data directories
- **options**, **tags**, **types** – feature modules used when parsing PAD templates
- **start**, **walk**, **sequence**, **level**, **occurrence**, **try** – internal runtime logic for processing templates

The entry point for the framework is `pad.php` in the repository root.

## Installation

PAD assumes PHP and MySQL are available. The `install` directory contains shell scripts that create database tables and configure an Apache environment. Adjust the paths in these scripts to match your system before running:

```bash
sudo install/install.sh
```

The framework relies on two constants that must be defined by the hosting application:

```php
define('APP', '/path/to/your/app');  // application directory
define('DAT', '/path/to/data');       // writable data directory
require '/path/to/pad/pad.php';
```

`APP` should contain the application specific PHP code and templates. `DAT` is used by PAD to store runtime data, logs and cache files.

## Usage

After installation and defining `APP` and `DAT`, include `pad.php` from your web server or CLI script to start processing PAD templates. Pages are typically stored as `.pad` files inside the application directory and can reference the helper functions and tags provided by this repository.

## License

No license file is provided with this repository. Check with the repository owner for usage terms.
