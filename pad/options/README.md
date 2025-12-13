# Options Processing

This module provides the options system for PAD templates, allowing tags to modify behavior, format output, and control data flow through declarative options.

## Overview

The options module implements a flexible system for processing tag options that modify template behavior at different stages (start, end, app, callback). Options can control output formatting, data storage, conditional logic, and callback execution. This provides a declarative way to customize template processing without complex logic.

## Core Files

### Output Formatting
- **quote.php** - Wraps content with specified quote characters
- **glue.php** - Adds separator between items (except after last)
- **open.php** - Prepends content at start of first occurrence
- **close.php** - Appends content at end of last occurrence
- **tidy.php** - Cleans up whitespace and formatting

### Data Storage
- **toData.php** - Stores processed data array to named variable
- **toContent.php** - Stores generated content/output to named variable
- **toBool.php** - Stores boolean result based on output/state

### Conditional Options
- **optional.php** - Marks tag as optional (empty parameter)
- **demand.php** - Marks tag as required (returns TRUE)
- **bool.php** - Retrieves or creates boolean flag value
- **ignore.php** - Wraps content in ignore tags to skip processing

### Callback System
- **callback.php** - Invokes application callback for custom processing
- **before.php** - Processes options before main content

### Processing Control
- **noError.php** - Suppresses error handling
- **dump.php** - Debug output of current state
- **print.php** - Direct output printing
- **data.php** - Data handling option
- **content.php** - Content handling option
- **else.php** - Alternative/fallback handling

## Go Subdirectory

The `go/` subdirectory contains the option processing orchestration:

- **options.php** - Main option processor that walks through tag options
- **start.php** - Processes options at template start
- **end.php** - Processes options at template end
- **app.php** - Processes application-specific options
- **callback.php** - Processes callback-stage options

## Key Features

### Multi-Stage Processing
Options are processed at different stages:
- **Start**: Before content generation (`padBase`)
- **End**: After content generation (`padResult`)
- **App**: Application-specific processing
- **Callback**: During callback execution

### Content Transformation
Options can wrap, modify, or replace content:
- Add prefixes/suffixes (open/close)
- Apply separators (glue)
- Add quotes or formatting
- Clean up whitespace (tidy)

### State Management
- Store results in named variables (toData, toContent, toBool)
- Create and check boolean flags
- Track option processing with `padDone()` to prevent re-execution

### Conditional Logic
- Optional vs required tags
- Boolean flag creation and checking
- Ignore blocks to skip processing

## Integration with PAD Framework

The options module integrates deeply with PAD's architecture:

1. **Tag System**: Every tag can specify options to modify behavior
2. **Content Pipeline**: Options transform content at multiple stages
3. **Variable System**: Options can store/retrieve global variables
4. **Callback System**: Options can trigger application callbacks
5. **Template Flow**: Options control conditional rendering and data flow

## Option Processing Flow

1. Tag parameters are parsed into `$padPrm[$pad]`
2. Option processor walks through parameters at appropriate stage
3. For each option in the stage's option list:
   - Check if not already done (`padIsDone()`)
   - Get parameter value (`padTagParm()`)
   - Mark as done (`padDone()`)
   - Include option file to execute logic
4. Updated content is stored back to appropriate variable

## Usage Pattern

Options are specified in PAD template tags:
```
{tagName toData="myData" quote="'" glue=", " open="[" close="]"}
```

Each option modifies the processing pipeline, providing composable template behaviors.
