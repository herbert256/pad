# Reference Application

## Introduction

Cross-reference and directory listing utilities for PAD applications.

## Structure

```
reference/
├── _data/           # Reference data
├── _inits.pad       # The frame every PAD application shares
├── _inits.php       # Its menu: the index's sections, from _data/reference.json
├── dir.pad/.php     # Directory browser
├── index.pad        # Reference index
└── pages.pad/.php   # Page listing
```

## Features

- **Directory browser**: Navigate PAD application structure
- **Page listing**: List all available pages
- **Cross-reference**: Find related content
- **Complete entries**: Every listed feature opens recorded examples or a detail page with
  its description, source and, where available, usage syntax and catalogue examples.
  Detail pages resolve names against `_data/reference.json`, so they also work before a
  reference rebuild and never depend on fabricated usage records.

## Purpose

Provides tools for exploring and understanding the structure of PAD applications, useful for developers learning the framework.

## Access

Via web browser: `http://server/reference/`
