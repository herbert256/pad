# Palette semantics and component conformance

`padPaletteMetadata(name)` returns scale type, intended usage, recommended distinct category count and light/dark mark-contrast guidance. Mark contrast is measured against white and #111827; recommendations are not accessibility certification. Color must remain supplementary to text, shapes or labels. Categorical palettes recommend their nonrepeating native color count; continuous palettes have no category-count recommendation.

`padPaletteUsage(name,type,categories,scheme)` returns incompatibility messages. `padPaletteSelect(name,type,steps,scheme)` rejects unknown/mismatched scales, invalid schemes/counts and categorical repetition, then returns deterministic colors. Existing `padPalette()` remains backward compatible. `widget:palette` exposes scale type in its accessible label and accepts optional `usage=` and `scheme=` to enforce those contracts.

`tests/subsystems/application-theme-matrix.php` renders every theme with text, links, a focusable control, disabled control and status roles and measures declared role pairs. Native input selects now share text/background and visible-focus styles; forced colors preserves a visible invalid border and focus outline. Print styles retain readable controls. Effects already gate CSS motion on the visitor preference; active script cleanup is covered by application browser fixtures.
