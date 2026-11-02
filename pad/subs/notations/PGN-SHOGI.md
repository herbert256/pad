# Chess records and shogi positions

```pad
{notation:pgn board}
[Event "Example"]
[Result "*"]
1. e4 {Central space} e5 2. Nf3 Nc6 *
{/notation:pgn}
{notation:shogi direction='sente'}start{/notation:shogi}
```

PGN supports one mainline: up to 64 tag pairs, 2,000 plies and 100 KB UTF-8 source, SAN-shaped tokens, move numbers, braces/semicolon comments, `$0`–`$255` annotations and a required result. Tag quotes/backslashes may be escaped. Nested comments, variations, nonstandard variants, unsupported SAN and inconsistent result tags fail. **SAN is checked structurally; moves are not executed and legality is not checked.** `board` renders only the supplied initial FEN (`SetUp "1"` required) or standard initial position, with `flip` and `size`. No selected later position is inferred from moves.

Shogi accepts `start` or a complete SFEN (`placement b|w hands move-number`). Nine ranks, promotions, inventories including pieces in hand and one king per side are checked; positions are not certified legally reachable. Handicaps may omit nonking pieces. Unsupported moves, drops and alternate notation are rejected. Tiles use font-independent Latin piece identities (`+` for promotion), face the correct player, and have accessible full piece names. `direction='gote'` rotates the board; `size` and `title` customize output.
