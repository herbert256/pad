# search:und

Language-neutral tokens: lowercase Unicode letters/numbers, retaining interior dot, underscore, colon, hyphen and apostrophe. No stemming, stop words or accent folding. The engine maximum token length is 64 code points. Other punctuation separates tokens. Cache identity includes language code. This is identifier token matching, not byte-exact whole-field matching.

## Example

```pad
{search:und 'catalog', q='PAD-42', fields='name'}{$name}{/search:und}
```

Run the focused fixture with `php tests/subsystems/search-und.php`.
