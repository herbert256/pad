# PAD Framework - Template Tags

## Overview

The `tags` module contains the implementation files for PAD template tags. These are the executable commands used in PAD templates (e.g., `{if}`, `{data}`, `{set}`, `{while}`, etc.). Each tag file contains the logic that executes when that tag is encountered during template processing.

## Purpose

Template tags provide the control structures, data manipulation, flow control, and utility functions available in PAD templates. They enable:

- Conditional logic and branching
- Loops and iteration
- Variable management
- Data access and manipulation
- File operations
- Template inclusion
- Flow control (redirects, AJAX)
- Debugging and error handling

## Tag Categories

### Control Flow Tags

**if.php** - Conditional execution with elseif support
- Evaluates boolean expressions
- Supports `{elseif}` blocks
- Parses content to find nested if statements
- Returns TRUE if condition matches

**case.php** - Multi-way conditional (switch statement)
- Compares basis value against multiple `{when}` conditions
- Sequential evaluation of cases
- Returns TRUE when match found

**switch.php** - Cycles through multiple options
- Rotates through option values on each call
- Uses counter to track current position
- Useful for alternating values/styles

**while.php** - Loop while condition is true
- Evaluates boolean condition
- Sets walk mode to 'next' to continue iteration
- Inverse behavior for 'until' tag

**until.php** - Loop until condition becomes true
- Inverse of while.php

**continue.php** - Skip to next iteration
- Used within loops

### Data and Variable Tags

**data.php** - Access data stores
- Delegates to tags/_go/store.php
- Primary data access mechanism

**get.php** - Include and execute a page/template
- Delegates to start/enter/get.php
- Retrieves and processes external templates

**set.php** - Set level-scoped variables
- Cannot be used as open/close tag
- Sets variables from $padSetLvl into $GLOBALS
- Clears save and delete level arrays

**field.php** - Field access/manipulation
- Works with data fields

**array.php** / **record.php** - Array/record operations
- Both delegate to record.php
- Database/data record handling

**make.php** - Create/construct data
- Data construction operations

**pull.php** - Extract/retrieve data
- Data extraction operations

**keep.php** - Preserve data
- Data preservation operations

**remove.php** - Delete/remove data
- Data removal operations

### Execution and Include Tags

**page.php** - Include a page template
- Delegates to start/enter/page.php
- Page inclusion mechanism

**code.php** - Execute code block
- Delegates to start/enter/code.php
- Clears content before execution

**sandbox.php** - Execute code in isolated environment
- Identical to code.php
- Provides isolation

**action.php** - Perform an action
- Action execution tag

**restart.php** - Restart execution
- Restart mechanism

**redirect.php** - HTTP redirect
- Delegates to start/enter/redirect.php
- Redirects browser to new URL

**ajax.php** - AJAX request handling
- Delegates to start/enter/ajax.php
- Handles AJAX responses

### File and Directory Tags

**files.php** - List files in directory
- Supports recursive scanning
- File filtering by mask/pattern
- Directory-only or file-only modes
- Hidden file inclusion option
- Base path options (app/data/pad)
- Returns array of file information (path, file, ext, dir, item)

**dir.php** - Simple directory listing
- Returns scandir() results
- Basic directory contents

**exists.php** - Check file existence
- Returns file_exists() result

**file.php** - File operations
- File manipulation

**open.php** - Open file/resource
- Resource opening

**close.php** - Close file/resource
- Resource closing

**content.php** - Get/set content
- Content manipulation

### Output and Formatting Tags

**echo.php** - Output expression value
- Evaluates and outputs first option
- Returns padEval($padOpt[$pad][0])

**output.php** - Output generation
- Output handling

**dump.php** - Dump variable/data for debugging
- Debug output

**trace.php** - Execution trace
- Debug tracing

**tidy.php** - Clean/format output
- Output formatting

### Boolean and Type Tags

**true.php** - Return TRUE constant

**false.php** - Return FALSE constant

**null.php** - Return NULL constant

**bool.php** - Boolean type conversion

**check.php** - Check/validate condition
- Validation operations

### Counter Tags

**count.php** - Check if array/variable has elements
- Checks $padDataStore or $GLOBALS
- Returns TRUE if count > 0, FALSE if empty

**increment.php** - Increment a variable
- Increments $GLOBALS[$padField]
- Initializes to 1 if not set

**decrement.php** - Decrement a variable
- Decrements $GLOBALS[$padField]
- Initializes to -1 if not set

**sequence.php** - Generate sequence
- Sequence generation

### Error Handling and Flow Control Tags

**error.php** - Trigger error message
- Calls padError($padParm)
- Error generation

**exception.php** - Throw exception
- Exception handling

**exit.php** - Exit execution
- Terminates execution

**ignore.php** - Ignore/skip block
- Skips content

**flag.php** - Set/check flags
- Flag management

### Utility Tags

**curl.php** - HTTP requests via cURL
- External HTTP requests

**foo.php** - Test/placeholder tag
- Testing/development

**pad.php** - PAD framework operations
- Framework-level operations

### Subdirectories

**tags/_go/** - Navigation and flow control
- **data.php** - Data navigation
- **store.php** - Store access/navigation

## Key Implementation Patterns

### Tag Parameter Access

Tags access their parameters through global arrays:
- **$padParm** - Primary parameter value
- **$padOpt** - Options array (indexed parameters)
- **$padParms** - Complete parameters structure
- **$pad** - Current level/index

### Return Values

Tags return different values based on purpose:
- **TRUE/FALSE** - Success/failure, condition results
- **Arrays** - Data sets for iteration
- **Strings** - Output content
- **NULL** - No output/skip

### Common Helper Functions

Tags frequently use:
- **padEval()** - Evaluate expressions
- **padEvalBool()** - Evaluate as boolean
- **padTagParm()** - Get tag parameters
- **padError()** - Trigger errors
- **padFieldName()** - Get field names

## Integration with PAD Framework

Template tags integrate with:

- **/pad/start/** - Execution framework (via delegates)
- **/pad/tag/** - Property accessors (provide context)
- **/pad/level/** - Level processing
- **/pad/build/** - Template building

Tags are invoked during template parsing when the PAD framework encounters tag syntax like `{tagname parameters}`. The appropriate tag file is included and executed within the current execution context.

## Usage in Templates

Tags are used with curly brace syntax:

```
{if condition}
  Content when true
{elseif other_condition}
  Alternative content
{/if}

{data users}
  {name} - {email}
{/data}

{set myvar = "value"}
{echo myvar}

{files dir="images" mask="*.jpg" onlyFiles recursive}
  <img src="{path}">
{/files}
```

## Design Philosophy

The tags module follows these principles:

1. **Delegation** - Many tags delegate to specialized modules (start/enter/)
2. **Minimal Logic** - Tags contain execution logic, not business logic
3. **Global Context** - Tags operate on global framework variables
4. **Return Semantics** - Return values control template flow and output
5. **Parameter Flexibility** - Support both named and positional parameters
