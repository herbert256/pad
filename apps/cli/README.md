# CLI Application

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
1. Sets `$padApp` to the directory name (`cli`)
2. Includes the shared bootstrap from `www/pad.php`
3. PAD processes `index.pad` and outputs to the console

## Output Type

For CLI applications, set the output type in `_config/config.php`:

```php
$padOutputType = 'console';
```
