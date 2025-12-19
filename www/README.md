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

| Directory | Application |
|-----------|-------------|
| `pad/` | Reference app (manual + tests) |
| `demo/` | Demo application |
| `hello/` | Hello World example |
| `minimal/` | Minimal example |
| `support/` | Support utilities |
| `test/` | Test application |
| `nono/` | Nono application |

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
3. Access via `http://server/newapp/`

## Documentation

For PAD framework documentation, see [../pad/README.md](../pad/README.md).
