# PAD Functions Documentation

This document describes the available helper functions in the PAD framework. Functions are located in the `functions/` directory and are typically used within variable tags (e.g., `{$var | function}`).

## String Manipulation

-   **`capitalize`**: Capitalizes the first letter of each word (`ucwords`).
-   **`lower`**: Converts string to lowercase (`strtolower`).
-   **`upper`**: Converts string to uppercase (`strtoupper`).
-   **`trim`**: Strips whitespace from the beginning and end of a string (`trim`).
-   **`white`**: Collapses multiple whitespace characters into a single space.
-   **`replace(search, replace)`**: Replaces all occurrences of the search string with the replacement string.
-   **`substr(start, [length])`**: Returns a substring.
-   **`left(length)`**: Returns the left `length` characters.
-   **`right(length)`**: Returns the right `length` characters.
-   **`mid(start, length)`**: Returns a substring starting at `start` with length `length`.
-   **`after(delimiter)`**: Returns the substring after the first occurrence of the delimiter.
-   **`after_last(delimiter)`**: Returns the substring after the last occurrence of the delimiter.
-   **`before(delimiter)`**: Returns the substring before the first occurrence of the delimiter.
-   **`before_last(delimiter)`**: Returns the substring before the last occurrence of the delimiter.
-   **`contains(substring)`**: Checks if the string contains the substring.
-   **`like(pattern)`**: Performs SQL-like pattern matching (supports `%` and `_`).
-   **`max_len(length)`**: Truncates the string if it exceeds the specified length.
-   **`ucwords`**: Capitalizes the first letter of each word.

## Formatting & Escaping

-   **`html`**: Converts special characters to HTML entities (`htmlspecialchars`).
-   **`url`**: URL-encodes the string (`urlencode`).
-   **`slashes`**: Adds backslashes to special characters (`addslashes`).
-   **`stripslashes`**: Removes backslashes (`stripslashes`).
-   **`sanitize`**: Sanitizes the string using `FILTER_SANITIZE_FULL_SPECIAL_CHARS`.
-   **`strip_low`**: Strips characters with ASCII value < 32.
-   **`encode_high`**: Encodes characters with ASCII value > 127.
-   **`nbsp`**: Replaces spaces with non-breaking spaces (`&nbsp;`).

## Date & Time

-   **`now`**: Returns the current timestamp (`time()`).
-   **`time`**: Formats a timestamp (alias for `date`).
-   **`timestamp`**: Formats a timestamp (alias for `date`).
-   **`date([format], [timestamp])`**: Formats a local time/date. If no arguments, uses default format.

## Logic & Validation

-   **`exists`**: Checks if the value corresponds to an existing file path relative to `APP`.
-   **`in(array)`**: Checks if the value exists in the provided array.
-   **`range(min, max)`**: Checks if the value is between `min` and `max` (inclusive).
-   **`between(min, max)`**: Checks if the value is strictly between `min` and `max`.
-   **`optional`**: Returns the value if set, otherwise returns an empty string.

## Templating Helpers

-   **`open`**: Wraps the value in curly braces: `{$value}` -> `{{$value}}`.
-   **`close`**: Wraps the value in closing tag format: `{$value}` -> `{/$value}`.
-   **`tag`**: Wraps the value in curly braces: `{$value}` -> `{{$value}}`.

## Math

-   **`sqrt`**: Calculates the square root.
