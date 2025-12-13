# PAD Framework - Tag Property Accessors

## Overview

The `tag` module provides property accessors that retrieve metadata and state information about the current iteration context in PAD templates. These properties are accessed using the `@` symbol (e.g., `{@first}`, `{@count}`, `{@key}`) and provide information about loop iterations, data, parameters, and options.

## Purpose

Tag properties allow templates to access contextual information during execution without explicit variable passing. They provide:

- Loop iteration state (first, last, current position, etc.)
- Data access for the current execution level
- Parameter and option values
- Field and variable access
- Metadata about the current tag execution context

## Property Categories

### Iteration State Properties

**border.php** - Returns TRUE if current iteration is first OR last
- Combines first and last checks
- Useful for applying special formatting to boundary items

**first.php** - Returns TRUE if current occurrence is 1
- Checks if this is the first iteration
- Uses current.php internally

**notFirst.php** - Returns TRUE if NOT the first iteration
- Inverse of first.php

**last.php** - Returns TRUE if current key is the last array key
- Uses array_key_last() to determine final element
- Requires: $padData, $padKey

**notLast.php** - Returns TRUE if NOT the last iteration
- Inverse of last.php

**middle.php** - Returns TRUE if iteration is neither first nor last
- Checks that both notFirst and notLast are true

**current.php** - Returns the current occurrence number
- Accesses $padOccur[$padIdx]

**even.php** - Returns TRUE if occurrence number is even
- Uses modulo operation: $padOccur[$padIdx] % 2 == 0

**odd.php** - Returns TRUE if occurrence number is odd
- Inverse of even.php

**done.php** - Returns TRUE when all iterations are complete
- Indicates iteration completion status

### Data Access Properties

**data.php** - Returns the complete data array for current level
- Accesses: $GLOBALS['padData'][$padIdx]

**key.php** - Returns the current array key
- Accesses: $padKey[$padIdx]

**keys.php** - Returns all keys for the current level data
- Provides array of available keys

**fields.php** - Returns array of field name/value pairs
- Iterates through $padCurrent[$padIdx]
- Returns: array with 'name' and 'value' keys for each field

**firstFieldName.php** - Returns the name of the first field
- Gets the first field's name from current data

**firstFieldValue.php** - Returns the value of the first field
- Gets the first field's value from current data

### Count and Remaining Properties

**count.php** - Returns total iteration count
- Returns max of data array count and occurrence counter
- Formula: max(count($padData[$padIdx]), $padOccur[$padIdx])

**remaining.php** - Returns number of iterations left
- Formula: count($padData[$padIdx]) - $padOccur[$padIdx]
- Returns 0 if negative

### Parameter and Option Access

**parameter.php** - Gets a named parameter value
- Accesses: $padPrm[$padIdx][$parm]
- Returns NULL if parameter not set

**parameters.php** - Returns all parameters for current level
- Provides complete parameter array

**option.php** - Gets a named option value
- Accesses: $padOpt[$padIdx][$parm]
- Returns NULL if option not set

**options.php** - Returns all options for current level
- Provides complete options array

### Variable and Naming Properties

**variable.php** - Gets a level-scoped variable
- Accesses: $padSetLvl[$padIdx][$parm]
- Returns NULL if variable not set

**variables.php** - Returns all level-scoped variables
- Provides complete variables array

**name.php** - Returns the name of the current tag/level
- Accesses: $padName[$padIdx]

## Key Global Variables

These properties rely on PAD framework global variables:

- **$padIdx** - Current index/level being accessed
- **$padData** - Multi-level data arrays
- **$padKey** - Current keys for each level
- **$padOccur** - Occurrence counters for iterations
- **$padCurrent** - Current data set being processed
- **$padPrm** - Parameters for each level
- **$padOpt** - Options for each level
- **$padSetLvl** - Level-scoped variables
- **$padName** - Tag/level names

## Usage Examples

In PAD templates, these properties are accessed with the `@` symbol:

```
{data people}
  {@first ? <ul>}
  <li>{@current}. {name} {@even ? (even) : (odd)}</li>
  {@last ? </ul>}
{/data}

Total: {@count} people
Remaining: {@remaining}
Current key: {@key}
```

## Integration with PAD Framework

The tag property accessors integrate with:

- **Loop execution** - Provide iteration state during data loops
- **Template rendering** - Supply contextual information to templates
- **Tag processing** - Expose tag parameters and options
- **Variable scoping** - Access level-scoped variables

These properties are evaluated during template execution when the PAD parser encounters `@` property references. They provide read-only access to the current execution state without modifying it.

## Design Pattern

All property accessors follow a consistent pattern:

1. Declare needed global variables
2. Access the appropriate array/value using $padIdx
3. Return the requested data or NULL
4. Perform minimal computation (usually just array access)

This lightweight design ensures fast property access during template rendering.
