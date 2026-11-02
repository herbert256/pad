# fretboard

`{notation:fretboard 'C major', tuning='E2 A2 D3 G3 B3 E4', firstfret=0, frets=12/}` shows every matching pitch across a fret range. Text is a root and one of major, minor, pentatonic (major), minor-pentatonic, dorian or chromatic. Tuning lists 1–12 notes, lowest string first; octaves are accepted but pitch-class membership is independent of octave.

`firstfret` is 0–24; `frets` is the span, 1–24, inclusive at both ends. `labels='intervals'` replaces note names with degrees relative to the root; root dots have a distinct fill. `direction='left'` reverses fret order while keeping text upright. `size` is width in pixels; `title` sets the accessible name. The description lists each displayed string, fret and note. This is a static scale diagram, not fingering advice.
