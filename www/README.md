# Web Server Entry Point

This directory is **not part of the PAD framework**. It contains example files showing how to call PAD applications from a web server's htdocs directory.

## Purpose

These files serve as entry points that:
1. Detect the platform (Linux/macOS/Windows)
2. Set the `APP` constant to point to a PAD application
3. Set the `DAT` constant to point to the data directory
4. Include the PAD framework to process the request

## Files

| File | Description |
|------|-------------|
| `index.php` | Main entry point for the default PAD application |
| `padHome.php` | Platform detection helper |

## Usage

Copy or symlink this directory (or individual files) to your web server's htdocs directory, then adjust the paths as needed for your environment.

## Documentation

For information about the PAD framework, see the [PAD Framework README](../pad/README.md).
