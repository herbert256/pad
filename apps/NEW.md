# Creating a New PAD Application

This guide explains how to set up a new PAD application from scratch.

## Quick Start

### 1. Create the Application Directory

```bash
mkdir -p apps/myapp
```

### 2. Create the Entry Point

Create `www/myapp/index.php`:

```php
<?php

  include __DIR__ . '/../padHome.php';

  define ( 'APP', "$padHome/apps/myapp/"  );
  define ( 'DAT', "$padHome/DATA/"        );

  include "$padHome/pad/pad.php";

?>
```

### 3. Create the Index Page

Create `apps/myapp/index.php` (returns data):

```php
<?php

  $title = 'My App';
  $message = 'Hello World!';

?>
```

Create `apps/myapp/index.pad` (template):

```
<h1>{$title}</h1>
<p>{$message}</p>
```

### 4. Access Your Application

Visit `http://yourserver/myapp/` in your browser.

---

## Application Structure

```
apps/myapp/
├── index.php           # Home page data
├── index.pad           # Home page template
│
├── _inits.php          # Runs BEFORE all pages (optional)
├── _inits.pad          # Wraps ALL pages - use @pad@ placeholder (optional)
├── _exits.php          # Runs AFTER all pages (optional)
├── _exits.pad          # Closing wrapper (optional)
│
├── _lib/               # Auto-included PHP functions
├── _include/           # Auto-included template snippets
├── _tags/              # Custom template tags
├── _functions/         # Custom pipe functions
├── _callbacks/         # Data iteration callbacks
├── _options/           # Custom tag options
├── _config/            # Application configuration
│   └── config.php
├── _data/              # Static data files (XML, JSON)
│
├── pagename.php        # Additional pages (data)
├── pagename.pad        # Additional pages (template)
│
└── subdir/             # Subdirectories can have their own
    ├── _inits.pad      #   wrappers and libraries
    ├── page.php
    └── page.pad
```

---

## URL Structure

Pages are accessed via query string:

| URL | Page |
|-----|------|
| `/myapp/` | `index.pad` |
| `/myapp/?about` | `about.pad` |
| `/myapp/?contact` | `contact.pad` |
| `/myapp/?admin/users` | `admin/users.pad` |

---

## Page Pairing

Every page consists of two files with the same name:

- `pagename.php` - Returns data (variables, arrays)
- `pagename.pad` - Template that renders the data

Example:

**users.php**:
```php
<?php

  $title = 'Users';

  $users = [
    [ 'name' => 'Alice', 'email' => 'alice@example.com' ],
    [ 'name' => 'Bob',   'email' => 'bob@example.com'   ],
  ];

?>
```

**users.pad**:
```
<h1>{$title}</h1>

<ul>
  {users}
    <li>{$name} - {$email}</li>
  {/users}
</ul>
```

---

## Global Wrapper (_inits.pad)

Wrap all pages with a common layout:

**_inits.pad**:
```html
<!DOCTYPE html>
<html>
<head>
  <title>{$title}</title>
</head>
<body>
  <nav>
    <a href="?index">Home</a>
    <a href="?about">About</a>
  </nav>

  @pad@

  <footer>&copy; 2025 My App</footer>
</body>
</html>
```

The `@pad@` placeholder is replaced with each page's content.

---

## Global Setup (_inits.php)

Run PHP code before all pages:

**_inits.php**:
```php
<?php

  // Default title
  $title = ucfirst ( $padPage );

  // Check authentication
  session_start ();
  $loggedIn = isset ( $_SESSION ['user'] );

?>
```

---

## Auto-Loaded Directories

### _lib/ - PHP Functions

Files in `_lib/` are automatically included.

**_lib/helpers.php**:
```php
<?php

  function formatDate ( $date ) {
    return date ( 'F j, Y', strtotime ( $date ) );
  }

?>
```

Use in any `.php` file:
```php
$formatted = formatDate ( '2025-01-15' );
```

### _include/ - Template Snippets

Templates in `_include/` become available as tags.

**_include/card.pad**:
```html
<div class="card">
  @content@
</div>
```

Use in templates:
```
{card}
  <h3>Title</h3>
  <p>Content here</p>
{/card}
```

### _tags/ - Custom Tags

Create custom template tags.

**_tags/button.php**:
```php
<?php

  $label = padTagParm ( 'label', 'Click' );
  $href  = padTagParm ( 'href', '#' );

  $padContent = "<a href=\"$href\" class=\"button\">$label</a>";

?>
```

Use in templates:
```
{button label="Submit" href="?submit"}
```

### _functions/ - Pipe Functions

Create custom pipe functions.

**_functions/money.php**:
```php
<?php

  return '$' . number_format ( $padContent, 2 );

?>
```

Use in templates:
```
{$price | money}
```

### _callbacks/ - Iteration Callbacks

Process data during iteration.

**_callbacks/totals.php**:
```php
<?php

  switch ( $padCallback ) {
    case 'init':
      $total = 0;
      break;
    case 'row':
      $total += $amount;
      break;
    case 'exit':
      // $total now contains sum
      break;
  }

?>
```

Use in templates:
```
{items callback="totals"}
  {$name}: {$amount}
{/items}
Total: {$total}
```

---

## Template Syntax Reference

### Variables
```
{$variable}                 Output variable
{$user.name}                Object/array property
{$items[0]}                 Array index
```

### Pipe Functions
```
{$name | upper}             Uppercase
{$text | trim | lower}      Chain multiple
{$date | date ('Y-m-d')}    With parameters
```

### Loops
```
{users}
  {$name}
{/users}
```

### Conditionals
```
{if $count > 0}
  Has items
{elseif $count == 0}
  Empty
{else}
  Invalid
{/if}
```

### Iteration Properties
```
{items}
  {@first}First item{/first}
  {@last}Last item{/last}
  {@even}Even row{/even}
  {@odd}Odd row{/odd}
  {@count} - Total count
  {@current} - Current index
{/items}
```

---

## Configuration (_config/config.php)

Override framework settings:

```php
<?php

  // Database connection
  $padSqlHost     = 'localhost';
  $padSqlDatabase = 'myapp';
  $padSqlUser     = 'myuser';
  $padSqlPassword = 'mypass';

  // Error handling: pad, boot, php, stop, exit, ignore, log, dump
  $padErrorAction = 'pad';

  // Debug mode: trace, stats, track, xml, xref
  // $padInfo = 'trace';

?>
```

---

## Data Storage

Use the `DAT` directory for writable data:

```php
<?php

  $dataFile = DAT . 'myapp/data.json';

  // Ensure directory exists
  if ( ! is_dir ( DAT . 'myapp' ) )
    mkdir ( DAT . 'myapp', 0755, TRUE );

  // Read
  $data = json_decode ( file_get_contents ( $dataFile ), TRUE );

  // Write
  file_put_contents ( $dataFile, json_encode ( $data ) );

?>
```

---

## Subdirectories

Create sections with their own wrappers:

```
apps/myapp/
└── admin/
    ├── _inits.pad      # Admin section wrapper
    ├── _inits.php      # Admin authentication check
    ├── index.pad
    ├── users.pad
    └── settings.pad
```

Access via `?admin/users`, `?admin/settings`, etc.

Each `_inits.pad` wraps content from its directory and below.

---

## Example: Minimal Application

**www/minimal/index.php**:
```php
<?php
  include __DIR__ . '/../padHome.php';
  define ( 'APP', "$padHome/apps/minimal/" );
  define ( 'DAT', "$padHome/DATA/" );
  include "$padHome/pad/pad.php";
?>
```

**apps/minimal/index.php**:
```php
<?php
  $greeting = 'Hello, PAD!';
?>
```

**apps/minimal/index.pad**:
```
<h1>{$greeting}</h1>
```

---

## Tips

1. **Start simple** - Begin with just `index.php` and `index.pad`
2. **Add wrapper later** - Create `_inits.pad` when you need common layout
3. **Use DATA for storage** - Never write to APP directory
4. **Check existing apps** - Look at `apps/pad/` and `apps/demo/` for examples
5. **URL format** - Always use `?page` format for internal links
