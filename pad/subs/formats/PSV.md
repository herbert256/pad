# Pipe-separated values

The `psv` format reads and writes records with `|` between cells, a first record of field names and CSV-style double quotes. A cell containing a pipe, newline or quote is quoted; a quote inside it is doubled. Backslashes are literal. Multiline quoted cells and UTF-8 values are preserved.

```php
$rows = padFormatRead ( 'psv', "name|score\nAda|42\nZoë|37" );
$text = padFormatWrite ( 'psv', $rows );
```

Choose PSV explicitly or use a `.psv` data file. It is never guessed from pipe characters, which may belong to prose or Markdown. It writes `text/plain` as an attachment; a general `Accept: text/plain` does not select it.

Headers are trimmed, nonempty and unique. Blank records are skipped, short records receive empty trailing cells, and a record wider than its header is an error with its source line. Cell whitespace is preserved. The writer uses every column present in the input rows and fills missing cells. Like CSV, it prefixes potentially executable spreadsheet text cells with an apostrophe; numeric negative values remain numbers. Nested values are written as JSON text. Empty data writes an empty string.
