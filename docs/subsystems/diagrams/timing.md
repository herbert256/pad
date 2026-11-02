# timing

Digital tracks use one line per signal: `clock: 0 1 0 1`. States are 0, 1, x (unknown), z (high impedance) or . (repeat preceding state). Each whitespace-separated state occupies one tick; shorter tracks end early. A first line `timing` and `#` comments are optional. `mark 2 "ready"` places a shared marker at tick boundary 2.

`tick=10, unit='ns'` labels time in original units. `direction='RL'` reverses time, with labels upright; LR is default. `title` names the SVG. Up to 32 tracks, 256 ticks per track, 64 markers and 100 KB source are accepted. Invalid syntax, leading repeats and out-of-range markers fail the strict check; lenient mode returns no diagram. This is PAD's small digital timing grammar, not WaveDrom or Mermaid compatibility.
