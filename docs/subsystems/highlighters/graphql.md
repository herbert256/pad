# highlight:graphql

Lexical highlighting for graphql; aliases: gql. Handles comments, escaped strings, numeric literals and keywords. Kotlin and GraphQL triple strings and C# verbatim strings are consumed as complete tokens. This does not parse or validate programs; interpolations inside strings stay string-coloured. Shared escaping prevents source text becoming HTML.

## Example

```pad
{widget:highlight 'graphql'}query Book { book(id: "<tag>") { title } } # hi{/widget:highlight}
```

Run the focused fixture with `php tests/subsystems/highlight-graphql.php`.
