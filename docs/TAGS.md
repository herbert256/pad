# PAD Tags Documentation

This document describes the available tags in the PAD framework. Tags are located in the `tags/` directory.

## Control Flow

-   **`{if condition}`**: Implements conditional logic. Supports `{elseif}` and `{else}`.
-   **`{while condition}`**: Implements a loop while the condition is true.
-   **`{until condition}`**: Implements a loop until the condition is true (uses `while` logic).
-   **`{switch value}`**: Implements a switch-case structure.
-   **`{case value}`**: Defines a case within a switch block.
-   **`{continue}`**: Continues to the next iteration of a loop/sequence.
-   **`{restart}`**: Restarts the processing of the current level or page.
-   **`{exit}`**: Exits the script execution.

## Data & Variables

-   **`{set name=value}`**: Sets a global variable. Can set multiple variables at once.
-   **`{increment name}`**: Increments a global variable.
-   **`{decrement name}`**: Decrements a global variable.
-   **`{array name}`**: Creates a new array in the global scope.
-   **`{content}`**: Stores the content of the tag or data source into a store.
-   **`{data}`**: Loads data from a file or source.
-   **`{null}`**: Returns `NULL`.
-   **`{true}`**: Returns `TRUE`.
-   **`{false}`**: Returns `FALSE`.
-   **`{bool}`**: Converts content to a boolean flag.
-   **`{dump variable}`**: Dumps the contents of a variable for debugging.

## File System & IO

-   **`{file path}`**: Reads and returns the content of a file.
-   **`{dir path}`**: Iterates over files in a directory.
-   **`{files}`**: Recursively iterates over files, with filtering options (include/exclude, extensions).
-   **`{exists path}`**: Checks if a file exists.
-   **`{check path}`**: Checks if a file exists (alias/similar to exists).
-   **`{make}`**: Helper for sequence/make operations.
-   **`{remove}`**: Helper for sequence/remove operations.

## Output & Formatting

-   **`{echo value}`**: Outputs the value.
-   **`{output type}`**: Sets the output type (e.g., 'web', 'file').
-   **`{tidy}`**: Tidies the output HTML.
-   **`{open}`**: Returns an open brace `{` (escaped as `&open;`).
-   **`{close}`**: Returns a close brace `}` (escaped as `&close;`).
-   **`{html}`**: Escapes HTML entities.

## Execution & External

-   **`{include file}`**: Includes and processes another PAD file (implied by `page` or direct include logic).
-   **`{page file}`**: Includes a page.
-   **`{script name}`**: Executes a shell script located in `_scripts/`.
-   **`{curl url}`**: Performs a CURL request.
-   **`{redirect url}`**: Redirects the browser to a new URL.
-   **`{action}`**: Includes an action script.
-   **`{ajax}`**: Includes an AJAX script.
-   **`{sandbox}`**: Executes code in a sandbox environment.
-   **`{code}`**: Executes raw PHP code.

## Sequences & Collections

-   **`{sequence}`**: Manages sequences of data.
-   **`{pull}`**: Pulls data from a sequence.
-   **`{keep}`**: Keeps data in a sequence.
-   **`{count}`**: Counts items.

## Miscellaneous

-   **`{pad}`**: Returns `TRUE`.
-   **`{foo}`**: Returns a test string "Foo tag from pad".
-   **`{banaan}`**: Returns "Geel" (Yellow).
-   **`{trace}`**: Enables tracing/logging for the block.
-   **`{error message}`**: Triggers a PAD error.
-   **`{exception message}`**: Throws an exception.
-   **`{ignore}`**: Ignores the content (returns true).
