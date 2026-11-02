# Format: logfmt

PAD's declared logfmt dialect is one record per line, with ASCII keys `[A-Za-z_][A-Za-z0-9_.-]*`, mandatory equals, and unquoted or double-quoted string values. Blank lines are ignored. Quoted escapes are `\n`, `\r`, `\t`, `\\`, `\"`; unsupported escapes, duplicates and malformed records fail with the source line number. Quoting is required for whitespace, quotes and backslashes. There is no universal logfmt standard; bare flags and nested values are intentionally rejected. Writers sort keys, stringify scalar values, encode booleans as true/false and end each record with LF. `padFormatRead('logfmt', 'level=info message="saved item"')` gives one row. Extension .logfmt, text/plain, rows shape; exact raw text, no auto-sniffing.

Both read and write use the existing padFormat helpers and strict/lenient error contract. Run `php tests/subsystems/format-logfmt.php`.
