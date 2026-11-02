# game:hex

Two local players on a 3..11 rhombus of hexagonal cells (size=7 default). Blue connects left/right, Orange top/bottom. Breadth-first connected-path win test uses all six hex neighbours, not line length. Visible coloured edge goals plus B/O letters distinguish players without colour dependence. Buttons support Enter/Space and arrow/Home/End navigation; shared game status announces turns/wins. No multiplayer service; without JS the goal description remains.

## Example

```pad
{game:hex size=5/}
```

Run the focused fixture with `node tests/subsystems/game-hex.cjs`.
