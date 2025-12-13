# PAD Framework - Start Module

## Overview

The `start` module is the core execution engine of the PAD framework. It manages the framework's startup sequence, execution flow, and context management. This module handles the initialization and termination of PAD operations, managing execution levels, and coordinating between different execution modes (page, code, function, sandbox).

## Purpose

This module provides the essential bootstrapping and execution infrastructure for the PAD framework. It:

- Initializes and manages execution contexts
- Handles different execution modes (normal, sandbox, clean, reset, function)
- Manages execution stack levels and variable scoping
- Coordinates the startup and shutdown sequences
- Provides entry points for different execution types (page includes, code execution, AJAX requests)

## Directory Structure

### Root Files

- **start.php** - Main startup orchestrator, increments counter and includes initialization files
- **end.php** - Shutdown orchestrator, handles cleanup and decrements counter
- **pad.php** - Primary execution wrapper that coordinates initialization, build, and exit phases
- **function.php** - Handles function-scoped execution with variable isolation
- **level.php** - Manages execution level tracking and progression
- **page.php** - Entry point for page building/rendering
- **code.php** - Entry point for code execution, stores code in $padBase
- **parms.php** - Parameter processing for sandbox, reset, clean, and function flags

### Subdirectories

#### start/start/ - Initialization Phase

Files executed during framework startup:

- **start.php** - Saves the current build context (build type, sandbox, clean, code, function, reset flags)
- **pad.php** - Manages PAD-level initialization
- **app.php** - Application-level initialization
- **dat.php** - Data-level initialization
- **stores.php** - Store initialization
- **level.php** - Level initialization logic
- **resetPad.php** - Resets PAD-level data structures and global stores
- **resetApp.php** - Resets application-level data structures

#### start/end/ - Termination Phase

Files executed during framework shutdown:

- **end.php** - Manages the shutdown sequence
- **pad.php** - PAD-level cleanup
- **app.php** - Application-level cleanup
- **dat.php** - Data-level cleanup
- **stores.php** - Store cleanup
- **unsetPad.php** - Unsets PAD-level variables
- **unsetApp.php** - Unsets application-level variables

#### start/enter/ - Entry Points

Different entry modes for framework execution:

- **start.php** - Standard entry point: initializes, executes level, then exits
- **page.php** - Page rendering entry: saves/restores page context, processes page includes
- **code.php** - Code execution entry: sets build mode to 'code' and stores code in $padBase
- **function.php** - Function-scoped entry: preserves $pad and $padOut across function calls
- **sandbox.php** - Sandbox execution (identical to code.php with clean content)
- **get.php** - GET request handling
- **ajax.php** - AJAX request handling
- **redirect.php** - HTTP redirect handling
- **restart.php** - Restart functionality

## Key Features

### Execution Modes

1. **Normal Mode** - Standard execution without special handling
2. **Sandbox Mode** (`sandbox` parameter) - Isolated execution environment
3. **Clean Mode** (`clean` parameter) - Execution with cleanup
4. **Reset Mode** (`reset` parameter) - Execution with data reset
5. **Function Mode** (`function` parameter) - Function-scoped execution with variable isolation

### Execution Flow

The typical execution sequence:

1. **Start Phase** (`start.php`)
   - Increment execution counter
   - Initialize PAD and APP contexts
   - Set up data stores
   - Reset data structures if needed
   - Initialize execution level

2. **Build Phase** (`pad.php` → `start/$padStrBld.php`)
   - Process parameters
   - Execute the appropriate build type (page/code/etc.)
   - Process template level logic

3. **End Phase** (`end.php`)
   - Clean up contexts
   - Unset temporary variables
   - Restore application state
   - Decrement execution counter

### Stack Management

The module maintains execution stack counters:

- **$padStrCnt** - Tracks nested start/end levels
- **$padStrFunCnt** - Tracks nested function calls
- **$padLevel** - Array tracking execution level progression

### Context Preservation

Various contexts are saved and restored:

- Page context (page, include, dir, path)
- Function context (pad level, output data)
- Build context (build type, flags, code)

## Integration with PAD Framework

The start module is the foundation of the PAD framework execution model:

- Works with `/pad/inits/` for variable initialization
- Coordinates with `/pad/level/` for template level processing
- Uses `/pad/build/` for template building
- Integrates with `/pad/exits/` for cleanup operations
- Manages execution of `/pad/occurrence/` for code occurrence tracking

This module essentially provides the runtime environment and execution lifecycle management for all PAD framework operations.

## Usage Context

This module is invoked automatically by the PAD framework and is not typically called directly by application code. It provides the infrastructure for:

- Template page includes via `{page}` tags
- Code block execution via `{code}` and `{sandbox}` tags
- Function calls with proper scoping
- AJAX and redirect operations
- GET request handling
