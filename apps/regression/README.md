# Regression Test Application

## Introduction

Automated regression testing for PAD framework validation.

## Structure

```
regression/
├── _inits.pad       # Test wrapper
├── _inits.php       # Test initialization
├── _lib/            # Test libraries
├── index.pad/.php   # Test dashboard
├── ok.php           # Success handler
└── show/            # Test result display
```

## Features

- **Test dashboard**: View all regression test results
- **Status tracking**: Track pass/fail status per test
- **Result comparison**: Compare current output against expected
- **Organized by app**: Tests grouped by application

## How It Works

1. Scans `DATA/regression/` for test result files (`.html`, `.txt`)
2. Groups results by application
3. Displays status for each test item
4. Allows re-running tests via the `go` parameter

## Test Results

Test results are stored in `DATA/regression/` with:
- `.html` files containing actual output
- `.txt` files containing status information

## Access

Via web browser: `http://server/regression/`
