# CLI Application

## Introduction

Command-line interface for running PAD applications from the terminal.

## Usage

Run PAD from the command line:

```bash
./pad
```

Or from another directory:

```bash
/path/to/apps/cli/pad
```

## Files

| File | Description |
|------|-------------|
| `pad` | Executable script that bootstraps PAD for CLI use |
| `index.pad` | Default template (outputs "Hello world") |
| `_config/config.php` | CLI-specific configuration |

## How It Works

The `pad` script:
1. Includes `home/home.php` to get `$padHome`, then sets `$padApps` and `$padData`
2. Sets `$padApp = 'cli'` and includes `pad/pad.php` directly
3. PAD processes `index.pad` and outputs to the console

## Output Type

For CLI applications, set the output type in `_config/config.php`:

```php
$padOutputType = 'console';
```

## Exit Status

The process status reports how the request ended, so scripts can test it: 0 when the
request finished with a 2xx or 3xx status, 1 for everything else - a PAD error, a missing
page, a request the boot net had to end. A failed request also prints a machine-readable
JSON error body, but the status alone is enough for a shell gate:

```bash
./pad mypage || echo "render failed"
```
