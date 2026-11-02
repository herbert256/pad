# Rack and Petri diagrams

`rack` numbers units from the bottom. `units` (1–64, default 42) must precede devices. Each device has a unique identifier, bottom unit, height in units, front label and optional rear label. Overlap and out-of-rack placement fail. `side='rear'` selects rear labels; physical unit numbering stays the same. Labels are plain text, at most 120 characters; quotes cannot be embedded.

```pad
{diagram:rack side='front'}
units 12
device switch 10 1 "Network switch" "24 network ports"
device server 4 2 "Compute server" "Power and management"
{/diagram:rack}
```

`petri` is a static ordinary weighted place/transition net: at most 60 nodes and 200 arcs, unique ASCII identifiers, labels up to 80 characters. Places have 0–99 tokens; transitions have no tokens. Arcs connect unlike node types and have weight 1–99. Unknown identifiers, duplicate arcs, inhibitor/reset arcs and simulation directives fail. Declaration order controls vertical layout, places on the left and transitions on the right. Long labels are shortened visually and retained in accessible titles.

```pad
{diagram:petri}
place ready "Ready" tokens=3
place done "Finished"
transition work "Process"
ready -> work weight=2
work -> done
{/diagram:petri}
```

Both support `title`, render deterministic escaped SVG, and require no JavaScript.
