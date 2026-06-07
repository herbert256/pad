# Apps Application

## Introduction

Lists all PAD applications with their descriptions, dynamically extracted from each application's README.md file.

## Features

- **Dynamic listing**: Scans the apps directory at runtime, so new applications appear immediately
- **Auto-descriptions**: Extracts the Introduction section from each app's README.md
- **Direct links**: Each application name links to its homepage

## How It Works

1. Scans the `apps/` directory for subdirectories
2. Skips hidden directories (`.`) and internal directories (`_`)
3. For each application, reads its `README.md` file
4. Extracts the content of the `## Introduction` section using regex
5. Displays a sorted table with links and descriptions

## Files

| File | Description |
|------|-------------|
| `index.php` | Scans apps directory and parses README files |
| `index.pad` | Template displaying the applications table |

## Access

Via web browser: `http://server/apps/`
