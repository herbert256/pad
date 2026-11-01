# PAD Reference Application

## Introduction

The main PAD framework reference and documentation application.

## Structure

```
pad/
├── _inits.pad/.php  # The frame every PAD application shares, its side the menu of the PAD
│                    #   applications - the brand's pull-down as a list (_common/_data/padApps.php)
├── _install/        # Installation utilities
├── index.pad/.php   # PAD introduction page
├── hello.pad/.php   # Hello World example
```

## Features

- **Framework introduction**: Explains the PAD concept
- **Hello World**: Classic introductory example
- **Installation tools**: Setup utilities

## Content

The index page presents:
- PAD in big letters on top (`www/pad/home.css`, with the side's menu)
- PAD's inversion of control concept
- The Hello World example
- Links to the classic "Don't call us, we'll call you" reference

## Access

Via web browser: `http://server/pad/`
