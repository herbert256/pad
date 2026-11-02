# highlight:kotlin

Lexical highlighting for kotlin; aliases: kt, kts. Handles comments, escaped strings, numeric literals and keywords. Kotlin and GraphQL triple strings and C# verbatim strings are consumed as complete tokens. This does not parse or validate programs; interpolations inside strings stay string-coloured. Shared escaping prevents source text becoming HTML.

## Example

```pad
{widget:highlight 'kotlin'}fun main() { println("<tag>") } // hi{/widget:highlight}
```

Run the focused fixture with `php tests/subsystems/highlight-kotlin.php`.
