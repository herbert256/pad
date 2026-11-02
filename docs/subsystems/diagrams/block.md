# block

A PAD grid grammar with explicit placement and spanning:

```
frontend "Web interface" at 1,1 span 2,1
api "Application API" at 1,2
store "Data store" at 2,2
frontend -> api: request
api -> store: query
```

Coordinates and spans are whole numbers starting at one; span defaults to 1,1. Grid size is at most 16×16; overlapping blocks and unknown endpoints fail validation. Limits are 64 nodes, 128 edges and 100 KB input. Blank lines, `#` comments and a `block` header are accepted. Labels are quoted and HTML-escaped; long labels wrap and are shortened only in the picture, with full text in the description/tooltips.

`direction` defaults to TB; BT reverses rows, LR transposes row/column placement, and RL transposes then reverses columns. Relationships use orthogonal paths and arrowheads; cycles and self-edges are supported without changing the explicit layout. Routes may cross other routes or intermediate blocks; the renderer does not solve arbitrary obstacle routing. `title` names the SVG. This is a bounded PAD grammar, not Mermaid block-beta compatibility.
