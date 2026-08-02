# Web Server Entry Points

This directory contains web server entry points for PAD applications. Point your web server's document root here.

## How It Works

The setup uses a centralized bootstrap pattern:

1. **App entry points** (`app/index.php`) just include `pad.php`
2. **pad.php** detects the OS, sets paths, derives the app from the request URL, and includes the PAD framework

### Request Flow

```
Browser → www/pad/index.php → www/pad.php → pad/pad.php
                ↓                   ↓
          includes ../pad.php  sets $padApp        defines APP, DAT
                               (from REQUEST_URI)  runs framework
```

## Files

| File | Description |
|------|-------------|
| `pad.php` | Centralized bootstrap: OS detection, path setup, app detection, framework inclusion |
| `index.php` | Root entry point (loads the `pad` app by default) |

## App Entry Points

Each app has a subdirectory with a minimal `index.php`:

```php
<?php
  include __DIR__ . '/../pad.php'
?>
```

The app name is derived in `pad.php` from the first `REQUEST_URI` path segment that matches a directory under `apps/`.

| Directory | Application | Static Assets |
|-----------|-------------|---------------|
| `apps/` | Application listing | - |
| `check/` | Test suite | - |
| `chess/` | Standalone static page (not a PAD app) | index.html |
| `cli/` | CLI app (web entry) | - |
| `demo/` | Demo application | style.css |
| `develop/` | Development tools | - |
| `hello/` | Hello World example | - |
| `manual/` | Interactive documentation | - |
| `nono/` | Plain PHP (non-PAD) | - |
| `pad/` | PAD reference app | level_demo.yaml |
| `react/` | PAD + React integration | JavaScript files (see below) |
| `reference/` | Cross-reference utilities | - |
| `regression/` | Regression testing | - |
| `sequence/` | Sequence demos | - |
| `structure/` | Directory structure demo | - |

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
├── counter/               # JavaScript for counter page
│   └── app.js             # CounterApp
└── topic/                 # JavaScript for topic page
    ├── display.js         # DisplayComponent
    ├── simple.js          # SimpleComponent
    └── test.js            # TestComponent
```

**Philosophy:** Just as PAD separates PHP from templates, the React app separates:
- **Data** (apps/react/_data/*.json) - Static data
- **PHP** (apps/react/*.php) - Server-side logic
- **Templates** (apps/react/*.pad) - HTML structure
- **JavaScript** (www/react/*/*.js) - Client-side interactivity

Templates reference external scripts: `<script type="text/babel" src="/react/[page]/[component].js"></script>`

## Bootstrap (pad.php)

The centralized bootstrap:

1. Detects OS (`lin`/`dar`/`win`)
2. Sets `$padHome` based on platform, plus `$padApps` and `$padData`
3. Derives `$padApp` from the first `REQUEST_URI` path segment matching a directory under `apps/` (falls back to `pad`)
4. Includes `pad/pad.php`, which defines the constants:
   - `APP` → `$padHome/apps/$padApp/`
   - `DAT` → `$padHome/DATA/`

## Adding a New App

1. Create the app in `apps/newapp/`
2. Create `www/newapp/index.php`:
   ```php
   <?php
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
