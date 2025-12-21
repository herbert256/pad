# Web Server Entry Points

This directory contains web server entry points for PAD applications. Point your web server's document root here.

## How It Works

The setup uses a centralized bootstrap pattern:

1. **App entry points** (`app/index.php`) set `$padApp` and include `pad.php`
2. **pad.php** detects the OS, sets paths, and includes the PAD framework

### Request Flow

```
Browser → www/pad/index.php → www/pad.php → pad/pad.php
                ↓                   ↓
          sets $padApp        sets APP, DAT
          (from dirname)      includes framework
```

## Files

| File | Description |
|------|-------------|
| `pad.php` | Centralized bootstrap: OS detection, path setup, framework inclusion |
| `index.php` | Root entry point (loads the `pad` app by default) |
| `info.php` | PHP info page |
| `DATA` | Symlink to `../DATA` runtime directory |

## App Entry Points

Each app has a subdirectory with a minimal `index.php`:

```php
<?php
  $padApp = basename ( __DIR__ );
  include __DIR__ . '/../pad.php'
?>
```

The app name is automatically derived from the directory name.

| Directory | Application | Static Assets |
|-----------|-------------|---------------|
| `pad/` | Reference app (manual + tests) | - |
| `demo/` | Demo application | style.css |
| `hello/` | Hello World example | - |
| `minimal/` | Minimal example | - |
| `support/` | Support utilities | - |
| `structure/` | Directory structure demo | - |
| `test/` | Test application | - |
| `nono/` | Nono application | - |
| `react/` | PAD + React integration | JavaScript files (see below) |

## React App Static Assets

The `react/` app demonstrates PAD's separation of concerns philosophy extended to client-side code. It contains external JavaScript files organized by page:

```
react/
├── index.php              # App entry point
├── index/                 # JavaScript for index page
│   ├── welcome.js         # WelcomeComponent
│   └── users.js           # UsersComponent
├── examples/              # JavaScript for examples page
│   ├── click.js           # ClickExample
│   ├── form.js            # FormExample
│   ├── products.js        # ProductList
│   └── toggle.js          # ToggleExample
├── components/            # JavaScript for components page
│   ├── demo.js            # ComponentsDemo (Card & Button)
│   └── todo.js            # TodoApp
└── counter/               # JavaScript for counter page
    └── app.js             # CounterApp
```

**Philosophy:** Just as PAD separates PHP from templates, the React app separates:
- **Data** (apps/react/_data/*.json) - Static data
- **PHP** (apps/react/*.php) - Server-side logic
- **Templates** (apps/react/*.pad) - HTML structure
- **JavaScript** (www/react/*/*.js) - Client-side interactivity

Templates reference external scripts: `<script type="text/babel" src="/react/[page]/[component].js"></script>`

## Bootstrap (pad.php)

The centralized bootstrap:

1. Requires `$padApp` to be set
2. Detects OS (`lin`/`dar`/`win`)
3. Sets `$padHome` based on platform
4. Defines constants:
   - `APP` → `$padHome/apps/$padApp/`
   - `DAT` → `$padHome/DATA/$padApp/`
5. Includes the PAD framework

## Adding a New App

1. Create the app in `apps/newapp/`
2. Create `www/newapp/index.php`:
   ```php
   <?php
     $padApp = basename ( __DIR__ );
     include __DIR__ . '/../pad.php'
   ?>
   ```
3. (Optional) Add static assets (CSS, JavaScript) in `www/newapp/`
4. Access via `http://server/newapp/`

**Static Assets:** Any files in `www/appname/` are served directly by the web server:
- CSS files: `www/appname/style.css` → `/appname/style.css`
- JavaScript: `www/appname/script.js` → `/appname/script.js`
- Organized subdirectories: `www/react/index/welcome.js` → `/react/index/welcome.js`

## Documentation

For PAD framework documentation, see [../README.md](../README.md).
