# Development Tools

## Introduction

Development utilities for building, testing, and debugging PAD applications.

## Structure

```
develop/
├── _include/        # Development includes
├── _inits.pad       # Development wrapper
├── _lib/            # Development libraries
├── benchmark/       # Performance benchmarks
├── sequence/        # Sequence development tools
├── index.pad/.php   # Development dashboard
├── build.php        # Build utilities
├── clean.php        # Cleanup utilities
├── errors.pad/.php  # The error dumps a crawl left behind, one line per dump
├── examples.php     # Example generator
├── nuts.pad/.php    # Miscellaneous utilities
├── reference.php    # Reference builder
└── regression.php   # Starts a fresh build of the regression suites (with go=)
```

## Features

- **Build tools**: Compile and prepare PAD applications
- **Benchmarks**: Performance testing utilities
- **Error analysis**: Debug and analyze PAD errors
- **Regression testing**: Run automated tests
- **Reference generation**: Build documentation

## Access

Via web browser: `http://server/develop/`
