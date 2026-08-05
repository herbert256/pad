# Web Server Entry Points

This directory contains web server entry points for PAD applications. Point your web server's document root here, or mount it under a URL prefix (e.g. a `pad` symlink in an existing docroot serving everything at `/pad/<app>/`) - `www/pad.php` detects the mount point (`$padRoot`) automatically and all generated links respect it.

## How It Works

The setup uses a centralized bootstrap pattern:

1. **App entry points** (`app/index.php`) just include `pad.php`
2. **pad.php** detects the OS, sets paths, derives the app from the request URL, and includes the PAD framework

### Request Flow

```
Browser → www/demo/index.php → www/pad.php → pad/pad.php
                ↓                   ↓
          includes ../pad.php  sets $padApp, $padRoot   defines APP, DATA
                               (from SCRIPT_NAME)       runs framework
```

## Files

| File | Description |
|------|-------------|
| `pad.php` | Centralized bootstrap: includes `home/home.php` for the machine-specific `$padHome`, then path setup, app detection, framework inclusion |
| `index.php` | Root entry point (loads the `pad` app by default) |
| `DATA` | Symlink to `../DATA` so runtime output (dumps, regression results) is browsable over HTTP (created by `pad/install/data.sh`; git-ignored) |

## App Entry Points

Each app has a subdirectory with a minimal `index.php`:

```php
<?php
  include __DIR__ . '/../pad.php'
?>
```

The app name and the mount prefix (`$padRoot`) are derived in `pad.php` from `SCRIPT_NAME`: the entry script's directory names the app, and anything above it is the mount prefix.

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

1. Includes `home/home.php` (OS detection → `$padHome`; the only place with machine-specific paths)
2. Sets `$padApps` and `$padData`
3. Derives `$padApp` and the URL mount prefix `$padRoot` from `SCRIPT_NAME`/`SCRIPT_FILENAME` (the entry directory names the app; falls back to `pad` for the root entry point)
4. Includes `pad/pad.php`, which defines the constants:
   - `APP` → `$padHome/apps/$padApp/`
   - `DATA` → `$padHome/DATA/`

Cross-app links are generated from `$padHost`, which includes the mount prefix (`scheme://host` . `$padRoot`, set in `pad/inits/host.php`), so the same tree works served at the root or under any prefix.

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
