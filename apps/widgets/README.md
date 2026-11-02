# Widgets Application

Page components and their examples share this gallery with landing-page sections, effects, themes and palettes. Interactive and text widgets formerly in Showcase now live under `examples/`.

## Introduction

The related page-design families of the engine, shown at work: the sections
(`pad/subs/widgets/sections/`) - the blocks a landing page is built of -, the effects
(`pad/subs/widgets/effects/`) - motion and decoration -, the themes (`pad/subs/themes/`) - the frame's
colours swapped - and the palettes (`pad/subs/themes/palettes/`) - colour scales for charts and
anything else. The home page is a landing page built from sections and effects; the theme
switcher, `{widget:themes/}`, stands in the bar of every page. A page per group shows every kind with
its examples, a page per kind its examples alone - a theme's page with the contrast of each of
its texts on each of its backgrounds and a button that puts every page in it, a palette's with
its swatches and charts in it. Every example is its template beside what it renders, with the
icons that take it to the WASM editor and the live application.

The kinds are read from the engine - the files of each subsystem's `types/` and the comment at
the top of each (`_lib/sections.php`) - so a kind added to the engine is here without a change
to this application.

## Files

| File | Description |
|------|-------------|
| `index.pad` / `index.php` | The landing page, built from sections and effects |
| `sections.pad`, `effects.pad`, `themes.pad`, `palettes.pad` (+ `.php`) | A group: every kind with its examples (`_include/groupPage.pad`) |
| `[group]/[kind].pad` / `.php` | A kind by the address - `?section/hero`, `?effect/typewriter`, `?theme/paper`, `?palette/viridis`; any other is a page not found |
| `_examples/section/<kind>.pad`, `_examples/effect/<kind>.pad` | The examples of the sections and the effects, a template each - `<kind>-2.pad` a second |
| `_include/exampleList.pad` | An example: its result, its template, the WASM editor icons |
| `_lib/sections.php` | The catalog: the groups, their kinds and their examples - a theme's and a palette's made here |
| `_inits.php` / `_inits.pad` | The shared frame, its menu a page per group and per kind, `{widget:themes/}` in its bar |
| `www/widgets/sections.css` | The example blocks, the contrast table |

## Access

`http://localhost/pad/widgets/`
